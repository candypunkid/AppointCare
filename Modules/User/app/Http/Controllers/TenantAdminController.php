<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\User;
use Illuminate\Http\Request;

class TenantAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'tenant_admin') {
            abort(403);
        }

        $tenant = $user->tenant;

        $tenantUserCount = User::where('tenant_id', $tenant->id)->count();
        $appointmentCount = Appointment::where('tenant_id', $tenant->id)->count();
        $requestCount = AppointmentRequest::where('tenant_id', $tenant->id)->count();
        $activeCustomers = User::where('tenant_id', $tenant->id)
            ->where('role', 'customer')
            ->count();

        return view('user::admin.tenant', [
            'user' => $user,
            'tenant' => $tenant,
            'tenantUserCount' => $tenantUserCount,
            'appointmentCount' => $appointmentCount,
            'requestCount' => $requestCount,
            'activeCustomers' => $activeCustomers,
            'newRequestCount' => AppointmentRequest::where('tenant_id', $tenant->id)->where('status', 'new')->count(),
            'recentRequests' => AppointmentRequest::where('tenant_id', $tenant->id)->latest()->take(10)->get(),
        ]);
    }
}
