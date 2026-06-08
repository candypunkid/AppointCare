@extends('layouts.admin')

@section('content')

<div class="max-w-7xl mx-auto space-y-8">

    <!-- HERO -->
    <div class="relative overflow-hidden rounded-[2.5rem] border border-white/10 bg-white/5 backdrop-blur-2xl p-10 shadow-2xl">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">

            <div>
                <p class="text-sm uppercase tracking-[0.4em] text-indigo-400 font-bold">
                    Admin Dashboard
                </p>

                <h1 class="mt-4 text-5xl font-extrabold tracking-tight bg-gradient-to-r from-white via-white to-indigo-300 bg-clip-text text-transparent">
                    Welcome back, {{ $user->name }}
                </h1>

                <p class="mt-4 text-slate-400 text-lg leading-relaxed max-w-2xl">
                    Here’s a quick overview of your platform performance, users, tenants, and system activity.
                </p>
            </div>

            <div class="rounded-3xl bg-slate-900/80 border border-white/10 backdrop-blur-md p-6 text-white shadow-2xl w-full lg:w-auto">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500 font-bold">Authenticated As</p>
                <p class="mt-3 text-xl font-mono text-indigo-300">{{ $user->email }}</p>

                <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-indigo-500/10 border border-indigo-500/20 px-4 py-1 text-xs font-bold text-indigo-300 uppercase tracking-widest">
                    Super Admin
                </div>
            </div>
        </div>

        {{-- Decorative Glow --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-600/10 blur-[120px] rounded-full"></div>
    </div>

    <!-- KPI CARDS -->
    <div class="grid gap-6 md:grid-cols-3">

        <div class="group rounded-3xl border border-white/10 bg-white/5 backdrop-blur-xl p-8 shadow-xl hover:bg-white/10 transition-all duration-300">
            <p class="text-sm uppercase tracking-widest text-slate-500 font-bold">Tenants</p>
            <p class="mt-4 text-6xl font-black text-white group-hover:text-indigo-400 transition-colors">{{ $tenantCount }}</p>
            <p class="mt-2 text-slate-400 font-medium">Active organizations</p>
        </div>

        <div class="group rounded-3xl border border-white/10 bg-white/5 backdrop-blur-xl p-8 shadow-xl hover:bg-white/10 transition-all duration-300">
            <p class="text-sm uppercase tracking-widest text-slate-500 font-bold">Platform Users</p>
            <p class="mt-4 text-6xl font-black text-white group-hover:text-indigo-400 transition-colors">{{ $platformUserCount }}</p>
            <p class="mt-2 text-slate-400 font-medium">Verified accounts</p>
        </div>

        <div class="group rounded-3xl border border-white/10 bg-white/5 backdrop-blur-xl p-8 shadow-xl hover:bg-white/10 transition-all duration-300">
            <p class="text-sm uppercase tracking-widest text-slate-500 font-bold">Super Admins</p>
            <p class="mt-4 text-6xl font-black text-white group-hover:text-indigo-400 transition-colors">{{ $superAdminCount }}</p>
            <p class="mt-2 text-slate-400 font-medium">System controllers</p>
        </div>
    </div>

    <!-- CHART SECTION -->
    <div class="grid lg:grid-cols-3 gap-6">

        <!-- BAR CHART -->
        <div class="lg:col-span-2 rounded-3xl border border-white/10 bg-white/5 backdrop-blur-2xl p-8 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white">Platform Overview</h2>
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20 uppercase tracking-widest">
                    Live Stats
                </span>
            </div>

            <canvas id="dashboardChart" height="120"></canvas>
        </div>

        <!-- SIDE INFO -->
        <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-2xl p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6">System Insights</h2>

            <div class="space-y-4 text-sm">

                <div class="p-5 rounded-2xl bg-white/5 border border-white/5 text-slate-300 leading-relaxed transition hover:bg-white/10">
                    📊 System is running smoothly with normal user activity.
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/5 text-slate-300 leading-relaxed transition hover:bg-white/10">
                    🚀 Tenant growth is stable this month.
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/5 text-slate-300 leading-relaxed transition hover:bg-white/10">
                    👥 Most users belong to tenant organizations.
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="grid lg:grid-cols-2 gap-6">

        <a href="{{ route('admin.tenants.index') }}"
           class="group relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-indigo-600 to-indigo-900 p-10 text-white shadow-2xl transition-all duration-300 hover:-translate-y-2 hover:shadow-indigo-500/20">

            <div class="relative z-10">
                <p class="text-xs uppercase tracking-[0.3em] text-indigo-200 font-bold">Infrastructure</p>
                <h2 class="mt-4 text-4xl font-black">Manage Tenants</h2>
                <p class="mt-3 text-indigo-100/80 text-lg">Provision and audit tenant organizations.</p>

                <div class="mt-8 flex items-center gap-2 text-sm font-bold uppercase tracking-widest">
                    Enter Console <span class="group-hover:translate-x-2 transition-transform">→</span>
                </div>
            </div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all"></div>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 backdrop-blur-xl p-10 text-white shadow-2xl transition-all duration-300 hover:-translate-y-2 hover:bg-white/10">

            <div class="relative z-10">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500 font-bold">Directory</p>
                <h2 class="mt-4 text-4xl font-black text-white">Platform Users</h2>
                <p class="mt-3 text-slate-400 text-lg">Control staff and customer access levels.</p>

                <div class="mt-8 flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-indigo-400">
                    Manage Users <span class="group-hover:translate-x-2 transition-transform">→</span>
                </div>
            </div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all"></div>
        </a>
    </div>
</div>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('dashboardChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Tenants', 'Platform Users', 'Super Admins'],
        datasets: [{
            label: 'Count',
            data: [
                {{ $tenantCount }},
                {{ $platformUserCount }},
                {{ $superAdminCount }}
            ],
            backgroundColor: [
                'rgba(99, 102, 241, 0.6)', // Indigo
                'rgba(139, 92, 246, 0.6)', // Violet
                'rgba(14, 165, 233, 0.6)'  // Sky
            ],
            borderColor: [
                'rgba(99, 102, 241, 1)',
                'rgba(139, 92, 246, 1)',
                'rgba(14, 165, 233, 1)'
            ],
            borderWidth: 2,
            borderRadius: 16,
            hoverBackgroundColor: 'rgba(255, 255, 255, 0.2)'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                ticks: { color: '#94a3b8', font: { weight: 'bold' } }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#94a3b8', font: { weight: 'bold' } }
            }
        }
    }
});
</script>

@endsection