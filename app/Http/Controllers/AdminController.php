<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\CarbonPeriod;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $company = auth()->user()->company;
        $companyId = $company->id;

        // KPIs du jour
        $todaySales = Sale::completed()->where('company_id', $companyId)->whereDate('created_at', today())->sum('total');
        $todaySalesCount = Sale::completed()->where('company_id', $companyId)->whereDate('created_at', today())->count();
        $todayExpenses = Expense::where('company_id', $companyId)->whereDate('expense_date', today())->sum('amount');
        $todayAvgBasket = $todaySalesCount > 0 ? round($todaySales / $todaySalesCount, 0) : 0;

        // KPIs du mois
        $monthSales = Sale::completed()->where('company_id', $companyId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');
        $monthPurchases = Purchase::where('company_id', $companyId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');
        $monthExpenses = Expense::where('company_id', $companyId)->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount');
        $monthGrossProfit = $monthSales - $monthPurchases;
        $monthNetProfit = $monthGrossProfit - $monthExpenses;

        // Stock
        $totalProducts = Product::where('company_id', $companyId)->count();
        $lowStockCount = Product::where('company_id', $companyId)->whereColumn('stock_quantity', '<=', 'alert_threshold')->count();
        $outOfStockCount = Product::where('company_id', $companyId)->where('stock_quantity', '<=', 0)->count();
        $stockValue = Product::where('company_id', $companyId)->sum(\DB::raw('stock_quantity * purchase_price'));

        // Creances clients
        $totalCustomerDebt = Customer::where('company_id', $companyId)->sum('total_debt');

        // Graphique activite 7 derniers jours
        $period = CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->endOfDay());
        $activityByDay = collect($period)->mapWithKeys(function ($day) use ($companyId): array {
            $date = $day->format('Y-m-d');
            return [$day->format('d/m') => Sale::completed()->where('company_id', $companyId)->whereDate('created_at', $date)->count()];
        });

        // Dernieres ventes
        $recentSales = Sale::with(['customer', 'user', 'shop'])
            ->where('company_id', $companyId)
            ->latest()
            ->limit(5)
            ->get();

        // Produits les plus vendus ce mois
        $topProducts = Product::select('products.id', 'products.name')
            ->selectRaw('SUM(sale_items.quantity) as qty_sold, SUM(sale_items.total) as revenue')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.company_id', $companyId)
            ->where('sales.status', 'completed')
            ->whereMonth('sales.created_at', now()->month)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'company' => $company,
            // Structure
            'usersCount' => $company->users()->count(),
            'shopsCount' => $company->shops()->count(),
            'warehousesCount' => $company->warehouses()->count(),
            // Jour
            'todaySales' => $todaySales,
            'todaySalesCount' => $todaySalesCount,
            'todayExpenses' => $todayExpenses,
            'todayAvgBasket' => $todayAvgBasket,
            // Mois
            'monthSales' => $monthSales,
            'monthGrossProfit' => $monthGrossProfit,
            'monthNetProfit' => $monthNetProfit,
            'monthExpenses' => $monthExpenses,
            // Stock
            'totalProducts' => $totalProducts,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'stockValue' => $stockValue,
            // Creances
            'totalCustomerDebt' => $totalCustomerDebt,
            // Graphique
            'activityLabels' => $activityByDay->keys()->values(),
            'activityValues' => $activityByDay->values(),
            // Tableaux
            'recentSales' => $recentSales,
            'topProducts' => $topProducts,
            // Activite
            'recentActivity' => AuditLog::where('company_id', $companyId)->latest()->limit(8)->get(),
        ]);
    }

    public function settings(): View
    {
        return view('admin.settings', [
            'company' => auth()->user()->company,
            'settings' => auth()->user()->company->settings()->orderBy('key')->get(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        auth()->user()->company->update($data);

        return back()->with('status', 'Paramètres de l\'entreprise enregistrés.');
    }

    public function activity(): View
    {
        return view('admin.activity', [
            'activities' => AuditLog::where('company_id', auth()->user()->company_id)->latest()->paginate(20),
        ]);
    }
}
