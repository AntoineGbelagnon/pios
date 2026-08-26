<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesReturn as ReturnModel;
use App\Models\ReturnItem;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function index(Request $request): View
    {
        $query = ReturnModel::with(['sale', 'customer', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('sale', fn($sq) => $sq->where('invoice_number', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) $query->where('status', $request->input('status'));

        return view('admin.returns.index', [
            'returns' => $query->latest()->paginate(20)->withQueryString(),
            'stats' => [
                'totalRefunded' => ReturnModel::where('status', 'completed')->sum('total_refund'),
                'pendingCount' => ReturnModel::where('status', 'pending')->count(),
                'monthRefunded' => ReturnModel::where('status', 'completed')->whereMonth('created_at', now()->month)->sum('total_refund'),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $sale = Sale::with(['items.product', 'customer'])->findOrFail($request->input('sale_id'));
        return view('admin.returns.create', compact('sale'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sale_id' => ['required', 'exists:sales,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sale_item_id' => ['required', 'exists:sale_items,id'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:500'],
            'refund_method' => ['required', 'in:cash,card,mobile_money,credit'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $result = DB::transaction(function () use ($data) {
            $sale = Sale::findOrFail($data['sale_id']);
            $totalRefund = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $saleItem = \App\Models\SaleItem::findOrFail($item['sale_item_id']);
                $itemTotal = $saleItem->unit_price * $item['quantity'];
                $totalRefund += $itemTotal;

                $itemsData[] = [
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $saleItem->unit_price,
                    'total' => $itemTotal,
                    'condition' => 'good',
                    'restock' => true,
                ];

                Product::where('id', $item['product_id'])->increment('stock_quantity', $item['quantity']);
            }

            $returnNumber = 'RET-' . now()->format('Ymd') . '-' . str_pad(ReturnModel::count() + 1, 5, '0', STR_PAD_LEFT);

            $return = ReturnModel::create([
                'company_id' => auth()->user()->company_id,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'user_id' => auth()->id(),
                'return_number' => $returnNumber,
                'type' => count($data['items']) === $sale->items->count() ? 'full' : 'partial',
                'total_refund' => $totalRefund,
                'refund_method' => $data['refund_method'],
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'status' => 'completed',
            ]);

            foreach ($itemsData as $itemData) {
                $return->items()->create($itemData);
            }

            if ($sale->customer_id && $sale->credit_amount > 0) {
                \App\Models\Customer::where('id', $sale->customer_id)->decrement('total_debt', $totalRefund);
            }

            return $return;
        });

        return redirect()->route('returns.show', $result)->with('status', 'Retour enregistre.');
    }

    public function show(ReturnModel $return): View
    {
        $return->load(['sale', 'customer', 'user', 'items.product']);
        return view('admin.returns.show', compact('return'));
    }
}
