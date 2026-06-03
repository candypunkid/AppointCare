<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Modules\User\Repositories\TenantRepository;

class TenantsController extends Controller
{
    public function index()
    {
        $filters = request()->only(['q', 'is_active', 'sort', 'direction']);
        $repo = new TenantRepository();
        $tenants = $repo->paginate($filters, 20);
        return view('user::admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('user::admin.tenants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
        ]);

        // if slug is empty, Tenant model will auto-generate
        $tenant = Tenant::create($data + ['is_active' => true]);

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant created');
    }

    public function edit(Tenant $tenant)
    {
        return view('user::admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $tenant->update($data);

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant updated');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('admin.tenants.index')->with('success', 'Tenant removed');
    }
}
