<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        return view('admin.products.brands.index', [
            'brands' => Brand::latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.brands.form', ['brand' => new Brand]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        auth()->user()->company->brands()->create($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('brands.index')->with('status', 'Marque créée.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.products.brands.form', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $brand->update($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('brands.index')->with('status', 'Marque modifiée.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();
        return redirect()->route('brands.index')->with('status', 'Marque supprimée.');
    }
}
