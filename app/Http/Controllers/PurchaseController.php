<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Purchase::with(['supplier', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) $query->where('status', $request->input('status'));

        return view('admin.purchases.index', [
            'purchases' => $query->latest()->paginate(20)->withQueryString(),
            'stats' => [
                'totalPending' => Purchase::whereIn('status', ['draft', 'ordered', 'partial'])->sum('total'),
                'totalReceived' => Purchase::where('status', 'received')->sum('total'),
                'totalCredit' => Purchase::sum('credit_amount'),
                'count' => Purchase::count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.purchases.create', [
            'suppliers' => Supplier::active()->orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
            'products' => Product::active()->orderBy('name')->get(),
            'reference' => 'ACH-' . now()->format('Ymd') . '-' . str_pad(Purchase::count() + 1, 5, '0', STR_PAD_LEFT),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'reference' => ['required', 'string', 'unique:purchases,reference'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,mobile_money,bank_transfer'],
            'expected_delivery_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $result = DB::transaction(function () use ($data) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemTotal;
                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity_ordered' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $itemTotal,
                ];
            }

            $discount = $data['discount_amount'] ?? 0;
            $tax = $data['tax_amount'] ?? 0;
            $total = $subtotal - $discount + $tax;
            $credit = max(0, $total - $data['amount_paid']);

            $purchase = Purchase::create([
                'company_id' => auth()->user()->company_id,
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'user_id' => auth()->id(),
                'reference' => $data['reference'],
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total' => $total,
                'amount_paid' => $data['amount_paid'],
                'credit_amount' => $credit,
                'payment_method' => $data['payment_method'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'ordered',
            ]);

            foreach ($itemsData as $itemData) {
                $purchase->items()->create($itemData);
            }

            return $purchase;
        });

        return redirect()->route('purchases.show', $result)->with('status', 'Commande creee.');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function receive(Request $request, Purchase $purchase): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.purchase_item_id' => ['required', 'exists:purchase_items,id'],
            'items.*.quantity_received' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($purchase, $data) {
            $allReceived = true;
            foreach ($data['items'] as $item) {
                $pi = PurchaseItem::findOrFail($item['purchase_item_id']);
                $pi->increment('quantity_received', $item['quantity_received']);

                if ($item['quantity_received'] > 0) {
                    Product::where('id', $pi->product_id)->increment('stock_quantity', $item['quantity_received']);
                }

                if ($pi->quantity_received < $pi->quantity_ordered) $allReceived = false;
            }

            $purchase->update([
                'status' => $allReceived ? 'received' : 'partial',
                'received_date' => now()->toDateString(),
            ]);
        });

        return back()->with('status', 'Reception enregistree.');
    }
}
