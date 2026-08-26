<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->input('period', 'month');
        $startDate = match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $salesQuery = Sale::completed()->where('created_at', '>=', $startDate);
        $expensesQuery = Expense::where('expense_date', '>=', $startDate->toDateString());
        $purchasesQuery = Purchase::where('created_at', '>=', $startDate);

        $totalSales = (clone $salesQuery)->sum('total');
        $totalCost = (clone $purchasesQuery)->sum('total');
        $totalExpenses = (clone $expensesQuery)->sum('amount');
        $grossProfit = $totalSales - $totalCost;
        $netProfit = $grossProfit - $totalExpenses;
        $salesCount = (clone $salesQuery)->count();
        $avgBasket = $salesCount > 0 ? $totalSales / $salesCount : 0;

        // Top produits
        $topProducts = Product::withSum('saleItems as total_revenue', 'total')
            ->withSum('saleItems as total_qty_sold', 'quantity')
            ->has('saleItems')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Top categories
        $topCategories = Category::query()
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->where('sales.created_at', '>=', $startDate)
            ->selectRaw('categories.id, categories.name, SUM(sale_items.quantity) as qty_sold, SUM(sale_items.total) as revenue')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Ventes par jour
        $salesByDay = Sale::completed()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Ventes par mode de paiement
        $salesByPayment = Sale::completed()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get();

        // Top clients
        $topCustomers = DB::table('customers')
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.status', 'completed')
            ->where('sales.created_at', '>=', $startDate)
            ->where('sales.company_id', auth()->user()->company_id)
            ->selectRaw('customers.id, customers.name, SUM(sales.total) as total_spent')
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Ventes par vendeur
        $salesByVendor = \App\Models\User::withSum(['sales' => fn($q) => $q->completed()->where('created_at', '>=', $startDate)], 'total as total_sales')
            ->withCount(['sales' => fn($q) => $q->completed()->where('created_at', '>=', $startDate)])
            ->has('sales')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return view('admin.statistics.index', compact(
            'totalSales', 'totalCost', 'totalExpenses', 'grossProfit', 'netProfit',
            'salesCount', 'avgBasket', 'topProducts', 'topCategories', 'salesByDay',
            'salesByPayment', 'topCustomers', 'salesByVendor', 'period'
        ));
    }

    public function exportSales(Request $request): Response
    {
        $period = $request->input('period', 'month');
        $startDate = $this->getStartDate($period);

        $sales = Sale::completed()
            ->with(['customer', 'user', 'shop'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();

        $csv = "Numero,Date,Client,Vendeur,Boutique,Total,Paye,Mode,Solde\n";
        foreach ($sales as $sale) {
            $csv .= '"' . $sale->invoice_number . '",';
            $csv .= '"' . $sale->created_at->format('d/m/Y H:i') . '",';
            $csv .= '"' . ($sale->customer->name ?? 'Comptoir') . '",';
            $csv .= '"' . ($sale->user->name ?? '') . '",';
            $csv .= '"' . ($sale->shop->name ?? '') . '",';
            $csv .= $sale->total . ',';
            $csv .= $sale->amount_paid . ',';
            $csv .= '"' . $sale->payment_method_label . '",';
            $csv .= $sale->credit_amount . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ventes_' . $period . '_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function exportProducts(Request $request): Response
    {
        $products = Product::with(['category', 'brand'])
            ->orderBy('name')
            ->get();

        $csv = "SKU,Nom,Categorie,Marque,Prix achat,Prix vente,Stock,Seuil alerte,Statut\n";
        foreach ($products as $p) {
            $csv .= '"' . $p->sku . '",';
            $csv .= '"' . str_replace('"', '""', $p->name) . '",';
            $csv .= '"' . ($p->category->name ?? '') . '",';
            $csv .= '"' . ($p->brand->name ?? '') . '",';
            $csv .= $p->purchase_price . ',';
            $csv .= $p->sale_price . ',';
            $csv .= $p->stock_quantity . ',';
            $csv .= $p->alert_threshold . ',';
            $csv .= '"' . $p->stock_status_label . "\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="produits_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function exportCustomers(Request $request): Response
    {
        $customers = Customer::orderBy('name')->get();

        $csv = "Nom,Email,Telephone,Type,Credit limite,Dette,Depenses totales,Points\n";
        foreach ($customers as $c) {
            $csv .= '"' . str_replace('"', '""', $c->name) . '",';
            $csv .= '"' . ($c->email ?? '') . '",';
            $csv .= '"' . ($c->phone ?? '') . '",';
            $csv .= '"' . $c->customer_type . '",';
            $csv .= $c->credit_limit . ',';
            $csv .= $c->total_debt . ',';
            $csv .= $c->total_spent . ',';
            $csv .= $c->loyalty_points . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="clients_' . date('Y-m-d') . '.csv"',
        ]);
    }

    private function getStartDate(string $period)
    {
        return match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }
}
