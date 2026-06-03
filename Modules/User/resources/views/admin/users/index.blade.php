@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-900">Platform Users</h1>
            <p class="mt-2 text-slate-600">Manage tenant admins, staff, and other platform users from one modern console.</p>
        </div>
            <div class="flex items-center gap-4">
            @php $canManageTenants = $canManageTenants ?? false; @endphp
            @if($canManageTenants)
                <a href="#" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700">Create user</a>
            @else
                <a href="#" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700">Create user</a>
            @endif
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email" class="rounded-md border px-3 py-2" />
                <select name="role" class="rounded-md border px-3 py-2">
                    <option value="">All roles</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="tenant_admin" {{ request('role') === 'tenant_admin' ? 'selected' : '' }}>Tenant Admin</option>
                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
                @if($canManageTenants)
                    <select name="tenant_id" class="rounded-md border px-3 py-2">
                        <option value="">All tenants</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" class="rounded-md bg-slate-900 text-white px-3 py-2">Filter</button>
            </form>
        </div>
    </div>

    <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-lg">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Role</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tenant</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @foreach($users as $u)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $u->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $u->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ ucfirst($u->role) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $u->tenant?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.users.edit', $u) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
</div>
@endsection
