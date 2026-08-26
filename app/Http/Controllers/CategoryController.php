<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.products.categories.index', [
            'categories' => Category::with('parent', 'children')
                ->whereNull('parent_id')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.categories.form', [
            'category' => new Category,
            'parentCategories' => Category::whereNull('parent_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        auth()->user()->company->categories()->create($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('categories.index')->with('status', 'Catégorie créée.');
    }

    public function edit(Category $category): View
    {
        return view('admin.products.categories.form', [
            'category' => $category,
            'parentCategories' => Category::whereNull('parent_id')
                ->where('id', '!=', $category->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $category->update($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('categories.index')->with('status', 'Catégorie modifiée.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->route('categories.index')->with('status', 'Catégorie supprimée.');
    }
}
