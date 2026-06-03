<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'super_admin') {
            abort(403);
        }

        return view('user::admin.super', [
            'user' => $user,
            'tenantCount' => Tenant::count(),
            'platformUserCount' => User::whereIn('role', ['tenant_admin', 'staff', 'customer'])->count(),
            'superAdminCount' => User::where('role', 'super_admin')->count(),
        ]);
    }
}
