<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->input('customer_type'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return view('admin.customers.index', [
            'customers' => $query->latest()->paginate(20)->withQueryString(),
            'stats' => [
                'total' => Customer::count(),
                'active' => Customer::where('is_active', true)->count(),
                'professionals' => Customer::where('customer_type', 'professional')->count(),
                'totalDebt' => Customer::sum('total_debt'),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.customers.form', [
            'customer' => new Customer,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'secondary_phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'customer_type' => ['required', 'in:individual,professional'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        auth()->user()->company->customers()->create($data + [
            'credit_limit' => $data['credit_limit'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('customers.index')->with('status', 'Client créé avec succès.');
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.form', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'secondary_phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'customer_type' => ['required', 'in:individual,professional'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $customer->update($data + [
            'credit_limit' => $data['credit_limit'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('customers.index')->with('status', 'Client modifié avec succès.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('status', 'Client supprimé avec succès.');
    }

    public function toggle(Customer $customer): RedirectResponse
    {
        $customer->update(['is_active' => !$customer->is_active]);
        return redirect()->route('customers.index')->with('status', 'Statut du client mis à jour.');
    }
}
