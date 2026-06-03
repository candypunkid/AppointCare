<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return view('user::admin.super', [
                'user' => $user,
                'tenantCount' => Tenant::count(),
                'platformUserCount' => User::whereIn('role', ['tenant_admin', 'staff', 'customer'])->count(),
                'superAdminCount' => User::where('role', 'super_admin')->count(),
            ]);
        }

        if ($user->role === 'tenant_admin') {
            return redirect()->route('tenant.dashboard');
        }

        $tenant = tenant();
        $users = User::forTenant($tenant)->paginate(15);

        return view('user::users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user::users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tenant = tenant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,staff,customer',
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('user.index')->with('success', 'User created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $tenant = tenant();
        $user = User::forTenant($tenant)->findOrFail($id);

        return view('user::users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tenant = tenant();
        $user = User::forTenant($tenant)->findOrFail($id);

        return view('user::users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tenant = tenant();
        $user = User::forTenant($tenant)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,staff,customer',
        ]);

        $user->update($validated);

        return redirect()->route('user.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tenant = tenant();
        $user = User::forTenant($tenant)->findOrFail($id);
        $user->delete();

        return redirect()->route('user.index')->with('success', 'User deleted successfully!');
    }
}
