<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\User\Repositories\UserRepository;

class TenantUsersController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'tenant_admin') {
            abort(403);
        }

        $filters = $request->only(['q', 'role', 'sort', 'direction']);
        $filters['tenant_id'] = $user->tenant_id;

        $repo = new UserRepository;
        $users = $repo->paginate($filters, 20);

        return view('user::admin.tenant_users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $current = auth()->user();
        if ($current->role !== 'tenant_admin' || $user->tenant_id !== $current->tenant_id) {
            abort(403);
        }

        return view('user::admin.tenant_users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $current = $request->user();
        if ($current->role !== 'tenant_admin' || $user->tenant_id !== $current->tenant_id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:staff,customer,tenant_admin',
        ]);

        $user->update($data);

        return redirect()->route('tenant.users.index')->with('success', 'User updated');
    }
}
