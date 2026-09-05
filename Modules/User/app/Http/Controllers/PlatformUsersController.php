<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\User\Repositories\UserRepository;

class PlatformUsersController extends Controller
{
    public function index()
    {
        $filters = request()->only(['q', 'role', 'tenant_id', 'sort', 'direction']);
        $repo = new UserRepository;
        $user = auth()->user();

        // If the current user is a tenant admin, force tenant filter to their tenant
        $isSuper = method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : ($user->role === 'super_admin');

        if (! $isSuper && $user->tenant_id) {
            $filters['tenant_id'] = $user->tenant_id;
        }

        $users = $repo->paginate($filters, 20);

        // load tenants list for filter (used only for super admins)
        $tenants = Tenant::orderBy('name')->get();

        $canManageTenants = $isSuper;

        return view('user::admin.users.index', compact('users', 'tenants', 'canManageTenants'));
    }

    public function edit(User $user)
    {
        return view('user::admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:super_admin,tenant_admin,staff,customer',
        ]);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
