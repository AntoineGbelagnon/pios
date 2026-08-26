<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Expense::with(['shop', 'user', 'cashRegister']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category')) $query->where('category', $request->input('category'));
        if ($request->filled('date_from')) $query->whereDate('expense_date', '>=', $request->input('date_from'));
        if ($request->filled('date_to')) $query->whereDate('expense_date', '<=', $request->input('date_to'));

        return view('admin.expenses.index', [
            'expenses' => $query->latest('expense_date')->paginate(20)->withQueryString(),
            'stats' => [
                'today' => Expense::whereDate('expense_date', today())->sum('amount'),
                'month' => Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'),
                'count' => Expense::count(),
            ],
            'categories' => Expense::CATEGORIES,
        ]);
    }

    public function create(): View
    {
        $cashRegister = CashRegister::open()->where('cashier_id', auth()->id())->first();
        return view('admin.expenses.create', [
            'categories' => Expense::CATEGORIES,
            'cashRegister' => $cashRegister,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:500'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,card,mobile_money,bank_transfer'],
            'cash_register_id' => ['nullable', 'exists:cash_registers,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Expense::create($data + [
            'company_id' => auth()->user()->company_id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('expenses.index')->with('status', 'Depense enregistree.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('status', 'Depense supprimee.');
    }
}
