@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-4xl font-bold tracking-tight bg-gradient-to-r from-white to-indigo-300 bg-clip-text text-transparent">Platform Users</h1>
            <p class="text-slate-400 mt-2">
                Manage tenant admins, staff, and customers in one unified console.
            </p>
        </div>

        <a href="#"
           class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            + Create User
        </a>
    </div>

    {{-- FILTER BAR --}}
    <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-4 shadow-xl mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}"
              class="flex flex-col lg:flex-row lg:items-center gap-3">

            <div class="flex-1">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Search by name or email..."
                       class="w-full rounded-xl border-white/10 bg-slate-900/60 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none transition" />
            </div>

            <select name="role"
                    class="rounded-xl border-white/10 bg-slate-900/60 px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                <option value="">All Roles</option>
                <option value="super_admin" @selected(request('role')=='super_admin')>Super Admin</option>
                <option value="tenant_admin" @selected(request('role')=='tenant_admin')>Tenant Admin</option>
                <option value="staff" @selected(request('role')=='staff')>Staff</option>
                <option value="customer" @selected(request('role')=='customer')>Customer</option>
            </select>

            @if($canManageTenants)
                <select name="tenant_id"
                        class="rounded-xl border-white/10 bg-slate-900/60 px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    <option value="">All Tenants</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" @selected(request('tenant_id')==$tenant->id)>
                            {{ $tenant->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <button type="submit"
                    class="rounded-xl bg-white text-slate-900 px-6 py-2.5 text-sm font-bold hover:bg-indigo-50 transition shadow-lg">
                Apply
            </button>
        </form>
    </div>

    {{-- TABLE CARD --}}
    <div class="backdrop-blur-2xl bg-white/5 border border-white/10 rounded-3xl shadow-2xl overflow-hidden">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-slate-300">Name</th>
                        <th class="text-left px-6 py-4 font-semibold text-slate-300">Email</th>
                        <th class="text-left px-6 py-4 font-semibold text-slate-300">Role</th>
                        <th class="text-left px-6 py-4 font-semibold text-slate-300">Tenant</th>
                        <th class="text-right px-6 py-4 font-semibold text-slate-300">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/5">
                    @forelse($users as $u)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4 font-medium text-white">
                                {{ $u->name }}
                            </td>

                            <td class="px-6 py-4 text-slate-400">
                                {{ $u->email }}
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $roleColors = [
                                        'super_admin' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                        'tenant_admin' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                        'staff' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'customer' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                    ];
                                @endphp

                                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $roleColors[$u->role] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                    {{ ucfirst(str_replace('_',' ', $u->role)) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-slate-400">
                                {{ $u->tenant?->name ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.users.edit', $u) }}"
                                   class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">
                                No users found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-6">
        {{ $users->links() }}
    </div>

</div>
@endsection