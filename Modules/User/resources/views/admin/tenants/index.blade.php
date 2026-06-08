@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-4xl font-bold tracking-tight bg-gradient-to-r from-white to-indigo-300 bg-clip-text text-transparent">Tenants</h1>
            <p class="text-slate-400 mt-2">
                Browse and manage tenant accounts with a clean and modern table experience.
            </p>
        </div>

        <a href="{{ route('admin.tenants.create') }}"
           class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            + New Tenant
        </a>
    </div>

    {{-- FILTER BAR --}}
    <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-4 shadow-xl mb-6">
        <form method="GET" action="{{ route('admin.tenants.index') }}"
              class="flex flex-col lg:flex-row lg:items-center gap-3">

            <div class="flex-1">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Search tenants by name or slug..."
                       class="w-full rounded-xl border-white/10 bg-slate-900/60 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none transition" />
            </div>

            <select name="is_active"
                    class="rounded-xl border-white/10 bg-slate-900/60 px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                <option value="">All Status</option>
                <option value="1" @selected(request('is_active') === '1')>Active</option>
                <option value="0" @selected(request('is_active') === '0')>Inactive</option>
            </select>

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
                        <th class="text-left px-6 py-4 font-semibold text-slate-300">Slug</th>
                        <th class="text-left px-6 py-4 font-semibold text-slate-300">Domain</th>
                        <th class="text-left px-6 py-4 font-semibold text-slate-300">Active</th>
                        <th class="text-right px-6 py-4 font-semibold text-slate-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($tenants as $t)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4 font-medium text-white">{{ $t->name }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $t->slug }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $t->domain ?? '—' }}</td>
                            <td class="px-6 py-4">
                                @if($t->is_active)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold border bg-emerald-500/10 text-emerald-400 border-emerald-500/20">Yes</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold border bg-slate-500/10 text-slate-400 border-slate-500/20">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.tenants.edit', $t) }}"
                                   class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">
                                No tenants found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $tenants->links() }}
    </div>
</div>
@endsection
