@php
    use App\Models\Tenant;
    $tenants = $tenants ?? collect();
@endphp

@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Tenants</h1>
            <p class="text-sm text-slate-400 mt-1">Manage all tenant organizations</p>
        </div>
        <a href="{{ route('admin.tenants.create') }}"
           class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-white font-semibold text-sm hover:opacity-90 transition">
            + New Tenant
        </a>
    </div>

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10 text-slate-400">
                    <th class="text-left px-5 py-4 font-medium">Name</th>
                    <th class="text-left px-5 py-4 font-medium">Slug</th>
                    <th class="text-left px-5 py-4 font-medium">Domain</th>
                    <th class="text-center px-5 py-4 font-medium">Users</th>
                    <th class="text-center px-5 py-4 font-medium">Appts</th>
                    <th class="text-center px-5 py-4 font-medium">Active</th>
                    <th class="text-right px-5 py-4 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            @if($tenant->logo_path)
                                <img src="{{ $tenant->logo_path }}" class="w-8 h-8 rounded-lg object-cover">
                            @else
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-violet-500 grid place-items-center text-white font-bold text-xs">
                                    {{ substr($tenant->name, 0, 2) }}
                                </div>
                            @endif
                            <span class="text-white font-medium">{{ $tenant->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-slate-300">{{ $tenant->slug }}</td>
                    <td class="px-5 py-4 text-slate-300">{{ $tenant->domain ?? '—' }}</td>
                    <td class="px-5 py-4 text-center text-slate-300">{{ $tenant->users_count }}</td>
                    <td class="px-5 py-4 text-center text-slate-300">{{ $tenant->appointments_count }}</td>
                    <td class="px-5 py-4 text-center">
                        @if($tenant->is_active)
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-semibold">Active</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-400 text-xs font-semibold">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('admin.tenants.edit', $tenant) }}"
                           class="px-4 py-1.5 rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 hover:text-white transition text-xs font-medium">
                            Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-slate-500">No tenants found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tenants->links() }}
    </div>
</div>
@endsection
