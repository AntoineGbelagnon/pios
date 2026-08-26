<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category', 'brand', 'warehouse');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->filled('stock_status')) {
            if ($request->input('stock_status') === 'out') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->input('stock_status') === 'low') {
                $query->where('stock_quantity', '>', 0)
                      ->whereColumn('stock_quantity', '<=', 'alert_threshold');
            }
        }

        return view('admin.stock.index', [
            'products' => $query->latest()->paginate(20)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'totalProducts' => Product::count(),
            'totalStockValue' => Product::sum(DB::raw('stock_quantity * purchase_price')),
            'lowStockCount' => Product::whereColumn('stock_quantity', '<=', 'alert_threshold')->count(),
            'outOfStockCount' => Product::where('stock_quantity', '<=', 0)->count(),
        ]);
    }

    public function movements(Request $request): View
    {
        $query = StockMovement::with('product', 'warehouse', 'creator');

        if ($search = $request->input('search')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($productId = $request->input('product_id')) {
            $query->where('product_id', $productId);
        }

        return view('admin.stock.movements', [
            'movements' => $query->latest()->paginate(20)->withQueryString(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function createMovement(): View
    {
        return view('admin.stock.form_movement', [
            'products' => Product::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function storeMovement(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'type' => ['required', 'in:entry,exit,adjustment,transfer'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);

            match($data['type']) {
                'entry' => $product->increment('stock_quantity', $data['quantity']),
                'exit' => $product->decrement('stock_quantity', $data['quantity']),
                default => null,
            };

            StockMovement::create($data + [
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
                'unit_price' => $data['unit_price'] ?? $product->purchase_price,
            ]);
        });

        return redirect()->route('stock.movements')->with('status', 'Mouvement de stock enregistre.');
    }

    public function createAdjustment(): View
    {
        return view('admin.stock.form_adjustment', [
            'products' => Product::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function storeAdjustment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'new_quantity' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            $previousQuantity = $product->stock_quantity;
            $difference = $data['new_quantity'] - $previousQuantity;

            $product->update(['stock_quantity' => $data['new_quantity']]);

            StockAdjustment::create([
                'company_id' => auth()->user()->company_id,
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $data['new_quantity'],
                'difference' => $difference,
                'reason' => $data['reason'],
                'created_by' => auth()->id(),
            ]);

            StockMovement::create([
                'company_id' => auth()->user()->company_id,
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'type' => 'adjustment',
                'quantity' => abs($difference),
                'notes' => $data['reason'],
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('stock.movements')->with('status', 'Ajustement de stock effectue.');
    }

    public function inventory(Request $request): View
    {
        $query = Product::with('category', 'brand');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        return view('admin.stock.inventory', [
            'products' => $query->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function storeInventory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.counted_quantity' => ['required', 'integer', 'min:0'],
        ]);

        $corrections = 0;

        DB::transaction(function () use ($data, &$corrections) {
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $systemQuantity = $product->stock_quantity;
                $countedQuantity = $item['counted_quantity'];
                $difference = $countedQuantity - $systemQuantity;

                if ($difference !== 0) {
                    $product->update(['stock_quantity' => $countedQuantity]);

                    StockAdjustment::create([
                        'company_id' => auth()->user()->company_id,
                        'product_id' => $product->id,
                        'previous_quantity' => $systemQuantity,
                        'new_quantity' => $countedQuantity,
                        'difference' => $difference,
                        'reason' => 'Inventaire physique - ' . now()->format('d/m/Y'),
                        'created_by' => auth()->id(),
                    ]);

                    StockMovement::create([
                        'company_id' => auth()->user()->company_id,
                        'product_id' => $product->id,
                        'type' => 'adjustment',
                        'quantity' => abs($difference),
                        'notes' => 'Inventaire physique',
                        'created_by' => auth()->id(),
                    ]);

                    $corrections++;
                }
            }
        });

        return redirect()->route('stock.inventory')->with('status', "Inventaire termine. {$corrections} correction(s) effectuee(s).");
    }

    public function lowStock(): View
    {
        $products = Product::with('category', 'brand')
            ->whereColumn('stock_quantity', '<=', 'alert_threshold')
            ->orderBy('stock_quantity')
            ->paginate(20);

        return view('admin.stock.low_stock', compact('products'));
    }
}
