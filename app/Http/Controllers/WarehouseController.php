<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(): View { return view('admin.warehouses.index', ['warehouses' => Warehouse::with('shop')->latest()->paginate(15)]); }

    public function create(): View { return view('admin.warehouses.form', ['warehouse' => new Warehouse, 'shops' => Shop::orderBy('name')->get()]); }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'shop_id' => ['nullable', 'exists:shops,id'], 'is_default' => ['boolean']]);
        auth()->user()->company->warehouses()->create($data + ['is_default' => false]);
        return redirect()->route('warehouses.index')->with('status', 'Entrepôt créé.');
    }

    public function edit(Warehouse $warehouse): View { return view('admin.warehouses.form', ['warehouse' => $warehouse, 'shops' => Shop::orderBy('name')->get()]); }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'shop_id' => ['nullable', 'exists:shops,id'], 'is_default' => ['boolean']]);
        $warehouse->update($data + ['is_default' => false]);
        return redirect()->route('warehouses.index')->with('status', 'Entrepôt modifié.');
    }
}
