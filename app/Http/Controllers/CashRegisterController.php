<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    public function index(Request $request): View
    {
        $query = CashRegister::with(['shop', 'cashier']);

        if ($request->filled('shop_id')) $query->where('shop_id', $request->input('shop_id'));
        if ($request->filled('status')) $query->where('status', $request->input('status'));

        return view('admin.cash_registers.index', [
            'registers' => $query->latest()->paginate(20)->withQueryString(),
            'shops' => Shop::orderBy('name')->get(),
            'openCount' => CashRegister::open()->count(),
            'todaySales' => Sale::completed()->whereDate('created_at', today())->sum('total'),
        ]);
    }

    public function create(): View
    {
        return view('admin.cash_registers.create', ['shops' => Shop::active()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'shop_id' => ['required', 'exists:shops,id'],
            'opening_amount' => ['required', 'numeric', 'min:0'],
            'opening_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $register = CashRegister::create($data + [
            'company_id' => auth()->user()->company_id,
            'user_id' => auth()->id(),
            'cashier_id' => auth()->id(),
            'status' => 'open',
        ]);

        return redirect()->route('cash_registers.show', $register)->with('status', 'Caisse ouverte.');
    }

    public function show(CashRegister $register): View
    {
        $register->load(['shop', 'cashier']);
        $sales = Sale::completed()->whereDate('created_at', $register->created_at->toDateString())->latest()->get();
        $expenses = Expense::where('cash_register_id', $register->id)->latest()->get();
        $register->total_sales = $sales->sum('total');
        $register->total_expenses = $expenses->sum('amount');
        $expectedAmount = $register->opening_amount + $register->total_sales - $register->total_expenses;

        return view('admin.cash_registers.show', compact('register', 'sales', 'expenses', 'expectedAmount'));
    }

    public function close(Request $request, CashRegister $register): RedirectResponse
    {
        $data = $request->validate([
            'closing_amount' => ['required', 'numeric', 'min:0'],
            'closing_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($register, $data) {
            $sales = Sale::completed()->whereDate('created_at', $register->created_at->toDateString())->sum('total');
            $expenses = Expense::where('cash_register_id', $register->id)->sum('amount');
            $expected = $register->opening_amount + $sales - $expenses;

            $register->update([
                'closing_amount' => $data['closing_amount'],
                'expected_amount' => $expected,
                'difference' => $data['closing_amount'] - $expected,
                'total_sales' => $sales,
                'total_expenses' => $expenses,
                'closing_notes' => $data['closing_notes'] ?? null,
                'status' => 'closed',
            ]);
        });

        return redirect()->route('cash_registers.index')->with('status', 'Caisse cloturee.');
    }
}
