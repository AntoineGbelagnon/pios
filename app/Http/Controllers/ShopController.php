<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View { return view('admin.shops.index', ['shops' => Shop::latest()->paginate(15)]); }

    public function create(): View { return view('admin.shops.form', ['shop' => new Shop]); }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:50'], 'address' => ['nullable', 'string', 'max:255'], 'city' => ['nullable', 'string', 'max:100'], 'phone' => ['nullable', 'string', 'max:30']]);
        auth()->user()->company->shops()->create($data + ['is_active' => true]);
        return redirect()->route('shops.index')->with('status', 'Boutique créée.');
    }

    public function edit(Shop $shop): View { return view('admin.shops.form', compact('shop')); }

    public function update(Request $request, Shop $shop): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:50'], 'address' => ['nullable', 'string', 'max:255'], 'city' => ['nullable', 'string', 'max:100'], 'phone' => ['nullable', 'string', 'max:30']]);
        $shop->update($data);
        return redirect()->route('shops.index')->with('status', 'Boutique modifiée.');
    }

    public function toggle(Shop $shop): RedirectResponse
    {
        $shop->update(['is_active' => ! $shop->is_active]);
        return back()->with('status', 'Statut de la boutique mis à jour.');
    }
}
