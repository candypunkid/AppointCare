@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <section class="rounded-[32px] border border-slate-200 bg-white p-8 shadow-xl mb-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-sm uppercase tracking-[0.3em] text-indigo-600 font-semibold">Admin dashboard</p>
                <h1 class="mt-4 text-4xl font-semibold text-slate-900">Welcome back, {{ $user->name }}.</h1>
                <p class="mt-4 text-slate-600 leading-8">Your modern admin panel gives you quick access to tenant management, platform users, and system controls all in one place.</p>
            </div>

            <div class="rounded-[32px] bg-slate-900 p-6 text-white shadow-2xl">
                <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Account</p>
                <p class="mt-4 text-2xl font-semibold">{{ $user->email }}</p>
                <span class="mt-4 inline-flex rounded-full bg-indigo-500/15 px-3 py-1 text-sm font-semibold text-indigo-200">Super Admin</span>
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-3 mb-8">
        <div class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-lg">
            <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Tenants</p>
            <p class="mt-5 text-5xl font-semibold text-slate-900">{{ $tenantCount }}</p>
            <p class="mt-3 text-slate-500">Total active tenant accounts in the system.</p>
        </div>

        <div class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-lg">
            <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Platform users</p>
            <p class="mt-5 text-5xl font-semibold text-slate-900">{{ $platformUserCount }}</p>
            <p class="mt-3 text-slate-500">Tenant admins, staff members, and customers combined.</p>
        </div>

        <div class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-lg">
            <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Super admins</p>
            <p class="mt-5 text-5xl font-semibold text-slate-900">{{ $superAdminCount }}</p>
            <p class="mt-3 text-slate-500">Users with full platform administration access.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <a href="{{ route('admin.tenants.index') }}" class="group block overflow-hidden rounded-[32px] bg-gradient-to-br from-indigo-600 to-slate-900 p-8 text-white shadow-2xl transition hover:-translate-y-1">
            <div class="text-sm uppercase tracking-[0.3em] text-indigo-200">Tenant management</div>
            <h2 class="mt-5 text-3xl font-semibold">Manage all tenants</h2>
            <p class="mt-3 text-slate-200/90">Quickly view, edit, or create new tenant accounts and monitor their status.</p>
            <div class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-white/90">
                Go to tenants
                <span class="inline-block transform transition group-hover:translate-x-1">→</span>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="group block overflow-hidden rounded-[32px] bg-white p-8 shadow-2xl border border-slate-200 transition hover:-translate-y-1">
            <div class="text-sm uppercase tracking-[0.3em] text-slate-400">User management</div>
            <h2 class="mt-5 text-3xl font-semibold text-slate-900">Platform users</h2>
            <p class="mt-3 text-slate-600">Review, update, and maintain tenant admin and staff accounts from one place.</p>
            <div class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-indigo-600">
                Manage users
                <span class="inline-block transform transition group-hover:translate-x-1">→</span>
            </div>
        </a>
    </div>
</div>
@endsection
