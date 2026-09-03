<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::with('company')
                ->where('company_id', auth()->user()->company_id)
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId(auth()->user()->company_id);

        return view('users.create', ['roles' => Role::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'company_id' => auth()->user()->company_id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        app(PermissionRegistrar::class)->setPermissionsTeamId(auth()->user()->company_id);
        $user->assignRole($data['role']);

        return redirect()->route('users.index')->with('status', 'Utilisateur créé avec succès.');
    }

    public function toggle(User $user): RedirectResponse
    {
        abort_unless($user->company_id === auth()->user()->company_id, 404);
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('status', 'Statut de l’utilisateur mis à jour.');
    }
}
