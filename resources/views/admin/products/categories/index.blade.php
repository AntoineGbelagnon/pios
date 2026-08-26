@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-secondary mb-1">Produits</p>
            <h1 class="h2 mb-0">Catégories</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('categories.create') }}">Nouvelle catégorie</a>
    </div>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <div class="card pios-panel border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Parent</th>
                        <th>Sous-catégories</th>
                        <th>Produits</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="fw-semibold">{{ $category->name }}</td>
                            <td>{{ $category->parent?->name ?: '—' }}</td>
                            <td>{{ $category->children_count ?? $category->children->count() }}</td>
                            <td>{{ $category->products_count ?? $category->products->count() }}</td>
                            <td>
                                <span class="badge text-bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('categories.edit', $category) }}">Modifier</a>
                                <form class="d-inline" method="POST" action="{{ route('categories.destroy', $category) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Aucune catégorie.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $categories->links() }}</div>
    </div>
@endsection
