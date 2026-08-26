<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarrantyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Warranty::with(['customer', 'product', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('warranty_number', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) $query->where('status', $request->input('status'));

        return view('admin.warranties.index', [
            'warranties' => $query->latest()->paginate(20)->withQueryString(),
            'stats' => [
                'activeCount' => Warranty::where('status', 'active')->count(),
                'expiringCount' => Warranty::where('status', 'active')->where('expiry_date', '<=', now()->addDays(30))->count(),
                'repairedCount' => Warranty::where('status', 'repaired')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.warranties.create', []);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'product_id' => ['required', 'exists:products,id'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'purchase_date' => ['required', 'date'],
            'duration_months' => ['required', 'integer', 'min:1'],
            'problem_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $warrantyNumber = 'GAR-' . now()->format('Ymd') . '-' . str_pad(Warranty::count() + 1, 5, '0', STR_PAD_LEFT);

        Warranty::create($data + [
            'company_id' => auth()->user()->company_id,
            'user_id' => auth()->id(),
            'warranty_number' => $warrantyNumber,
            'expiry_date' => now()->addMonths($data['duration_months']),
            'status' => 'active',
        ]);

        return redirect()->route('warranties.index')->with('status', 'Garantie enregistree.');
    }

    public function show(Warranty $warranty): View
    {
        $warranty->load(['customer', 'product', 'user']);
        return view('admin.warranties.show', compact('warranty'));
    }

    public function update(Request $request, Warranty $warranty): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:repaired,replaced,closed'],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
            'repair_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $warranty->update($data);
        return back()->with('status', 'Garantie mise a jour.');
    }
}
