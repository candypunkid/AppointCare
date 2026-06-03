<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} — Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <style>
        /* small helper to dim the sidebar on small screens */
        .sidebar-bg { background: linear-gradient(180deg, rgba(15,23,42,0.9), rgba(2,6,23,0.9)); }
    </style>
</head>
<body class="antialiased bg-gray-100 text-slate-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="hidden md:block w-72 sidebar-bg p-6">
            @php
                $currentUser = auth()->user();
                $isSuper = $currentUser ? (method_exists($currentUser, 'hasRole') ? $currentUser->hasRole('super_admin') : ($currentUser->role === 'super_admin')) : false;
                $dashboardRoute = $isSuper ? 'admin.dashboard' : ($currentUser && $currentUser->role === 'tenant_admin' ? 'tenant.dashboard' : 'user.index');
            @endphp
            <div class="mb-8">
                <a href="{{ route($dashboardRoute) }}" class="text-2xl font-bold text-white">{{ config('app.name') }}</a>
                <p class="text-sm text-gray-300">Admin Panel</p>
            </div>

            <nav class="space-y-2 text-gray-300">
                <a href="{{ route($dashboardRoute) }}" class="block py-2 px-3 rounded hover:bg-white/5">Dashboard</a>
                @if($isSuper)
                    <a href="{{ route('admin.tenants.index') }}" class="block py-2 px-3 rounded hover:bg-white/5">Tenants</a>
                    <a href="{{ route('admin.users.index') }}" class="block py-2 px-3 rounded hover:bg-white/5">Platform Users</a>
                @elseif($currentUser && $currentUser->role === 'tenant_admin')
                    <a href="{{ route('tenant.users.index') }}" class="block py-2 px-3 rounded hover:bg-white/5">My Users</a>
                @endif
                <a href="#" class="block py-2 px-3 rounded hover:bg-white/5">Settings</a>
            </nav>

            <div class="mt-8 text-sm text-gray-400">
                <div class="font-medium">{{ auth()->user()?->name }}</div>
                <div class="text-xs">{{ auth()->user()?->email }}</div>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button class="text-xs text-red-400 mt-2">Sign out</button>
                </form>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-1">
            <header class="bg-white p-4 md:hidden shadow-sm">
                <div class="flex items-center justify-between">
                    <a href="{{ route($dashboardRoute) }}" class="font-bold text-lg text-slate-900">{{ config('app.name') }}</a>
                    <div class="text-sm text-slate-700">{{ auth()->user()?->name }}</div>
                </div>
            </header>

            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
