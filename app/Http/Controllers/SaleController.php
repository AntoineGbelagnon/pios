<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sale::with(['customer', 'shop', 'user', 'items']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->input('shop_id'));
        }

        $sales = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'todayCount' => Sale::completed()->whereDate('created_at', today())->count(),
            'todayTotal' => Sale::completed()->whereDate('created_at', today())->sum('total'),
            'monthCount' => Sale::completed()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'monthTotal' => Sale::completed()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total'),
        ];

        return view('admin.sales.index', [
            'sales' => $sales,
            'stats' => $stats,
            'shops' => Shop::orderBy('name')->get(),
        ]);
    }

    public function pos(): View
    {
        $products = Product::active()
            ->with('category', 'brand')
            ->orderBy('name')
            ->get();

        $customers = Customer::active()->orderBy('name')->get();
        $shops = Shop::active()->orderBy('name')->get();

        return view('admin.sales.pos', [
            'products' => $products,
            'customers' => $customers,
            'shops' => $shops,
        ]);
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'shop', 'user', 'items.product']);

        return view('admin.sales.show', compact('sale'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'shop_id' => ['required', 'exists:shops,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payment_method' => ['required', 'in:cash,card,mobile_money,credit,mixed'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_details' => ['nullable'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $result = DB::transaction(function () use ($data) {
            $company = auth()->user()->company;
            $subtotal = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemDiscount = $item['discount_amount'] ?? 0;
                $itemTotal = ($item['unit_price'] * $item['quantity']) - $itemDiscount;
                $subtotal += $itemTotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $itemDiscount,
                    'total' => $itemTotal,
                ];

                $product->decrement('stock_quantity', $item['quantity']);
            }

            $discountPercent = $data['discount_percent'] ?? 0;
            $discountAmount = $subtotal * ($discountPercent / 100);
            $afterDiscount = $subtotal - $discountAmount;

            $taxPercent = $data['tax_percent'] ?? 0;
            $taxAmount = $afterDiscount * ($taxPercent / 100);
            $total = $afterDiscount + $taxAmount;

            $amountPaid = $data['amount_paid'];
            $creditAmount = max(0, $total - $amountPaid);
            $changeAmount = max(0, $amountPaid - $total);

            $invoiceNumber = 'INV-'.now()->format('Ymd').'-'.str_pad($company->sales()->count() + 1, 5, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'company_id' => $company->id,
                'shop_id' => $data['shop_id'],
                'user_id' => auth()->id(),
                'customer_id' => $data['customer_id'] ?? null,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_percent' => $discountPercent,
                'tax_amount' => $taxAmount,
                'tax_percent' => $taxPercent,
                'total' => $total,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'payment_method' => $data['payment_method'],
                'payment_details' => is_string($data['payment_details'] ?? null) ? json_decode($data['payment_details'], true) : ($data['payment_details'] ?? null),
                'credit_amount' => $creditAmount,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($itemsData as $itemData) {
                $sale->items()->create($itemData);
            }

            if ($creditAmount > 0 && ! empty($data['customer_id'])) {
                Customer::where('id', $data['customer_id'])->increment('total_debt', $creditAmount);
            }

            // Enregistrer le paiement
            $paymentMethod = $data['payment_method'];
            if ($amountPaid > 0) {
                $paymentDetails = is_string($data['payment_details'] ?? null) ? json_decode($data['payment_details'], true) : ($data['payment_details'] ?? []);
                if ($paymentMethod === 'mixed' && !empty($paymentDetails)) {
                    foreach ($paymentDetails as $detail) {
                        if (($detail['amount'] ?? 0) > 0) {
                            Payment::create([
                                'company_id' => $company->id,
                                'user_id' => auth()->id(),
                                'payable_id' => $sale->id,
                                'payable_type' => Sale::class,
                                'payment_number' => Payment::generatePaymentNumber(),
                                'amount' => $detail['amount'],
                                'payment_method' => $detail['method'] ?? $paymentMethod,
                                'direction' => 'in',
                                'reference' => $invoiceNumber,
                            ]);
                        }
                    }
                } else {
                    Payment::create([
                        'company_id' => $company->id,
                        'user_id' => auth()->id(),
                        'payable_id' => $sale->id,
                        'payable_type' => Sale::class,
                        'payment_number' => Payment::generatePaymentNumber(),
                        'amount' => $amountPaid,
                        'payment_method' => $paymentMethod,
                        'direction' => 'in',
                        'reference' => $invoiceNumber,
                    ]);
                }
            }

            return $sale;
        });

        return redirect()->route('sales.show', $result)->with('status', 'Vente enregistree avec succes.');
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
            }

            if ($sale->credit_amount > 0 && $sale->customer_id) {
                Customer::where('id', $sale->customer_id)->decrement('total_debt', $sale->credit_amount);
            }

            $sale->update(['status' => 'cancelled']);
        });

        return back()->with('status', 'Vente annulee.');
    }

    public function receipt(Sale $sale): View
    {
        $sale->load(['customer', 'shop', 'user', 'items.product']);
        $company = auth()->user()->company;
        return view('admin.sales.receipt.receipt', compact('sale', 'company'));
    }

    public function searchProducts(Request $request)
    {
        $query = $request->input('q', '');

        $products = Product::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get(['id', 'name', 'sku', 'barcode', 'sale_price', 'promo_price', 'stock_quantity', 'unit']);

        return response()->json($products);
    }
}
