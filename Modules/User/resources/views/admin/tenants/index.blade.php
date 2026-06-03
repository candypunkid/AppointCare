@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-900">Tenants</h1>
            <p class="mt-2 text-slate-600">Browse and manage tenant accounts with a clean and modern table experience.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.tenants.create') }}" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700">New tenant</a>
            <form method="GET" action="{{ route('admin.tenants.index') }}" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..." class="rounded-md border px-3 py-2" />
                <select name="is_active" class="rounded-md border px-3 py-2">
                    <option value="">All</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="rounded-md bg-slate-900 text-white px-3 py-2">Filter</button>
            </form>
        </div>
    </div>

    <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-lg">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Slug</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Domain</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Active</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @foreach($tenants as $t)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $t->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $t->slug }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $t->domain ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $t->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.tenants.edit', $t) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $tenants->links() }}</div>
</div>
@endsection
