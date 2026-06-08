<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} — Admin</title>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

@php
    $user = auth()->user();
    $isSuper = $user ? (method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : ($user->role === 'super_admin')) : false;

    $dashboardRoute =
        $isSuper
            ? 'admin.dashboard'
            : ($user && $user->role === 'tenant_admin'
                ? 'tenant.dashboard'
                : 'user.index');

    $active = fn($route) => request()->routeIs($route)
        ? 'bg-white/10 text-white shadow-lg'
        : 'text-slate-300 hover:bg-white/5 hover:text-white';
@endphp

<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white antialiased">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    <!-- MOBILE OVERLAY -->
    <div x-show="sidebarOpen"
         class="fixed inset-0 bg-black/60 z-40 md:hidden"
         @click="sidebarOpen=false">
    </div>

    <!-- SIDEBAR -->
   <aside
    class="fixed md:static z-50 w-72 h-screen bg-white/5 backdrop-blur-2xl
           border-r border-white/10 flex flex-col
           transform transition-transform duration-300"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

    <!-- BRAND -->
    <div class="p-6 border-b border-white/10">
        <a href="{{ route($dashboardRoute) }}"
           class="text-xl font-bold tracking-tight bg-gradient-to-r from-white to-indigo-300 bg-clip-text text-transparent">
            {{ config('app.name') }}
        </a>
        <p class="text-xs text-slate-400 mt-1">Admin Control Panel</p>
    </div>

    <!-- NAV -->
    <nav class="p-4 space-y-2 flex-1 overflow-y-auto">

        <a href="{{ route($dashboardRoute) }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ $active($dashboardRoute) }}">
            🏠 Dashboard
        </a>

        @if($isSuper)
            <a href="{{ route('admin.tenants.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ $active('admin.tenants.index') }}">
                🏢 Tenants
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ $active('admin.users.index') }}">
                👥 Platform Users
            </a>
        @elseif($user && $user->role === 'tenant_admin')
            <a href="{{ route('tenant.users.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ $active('tenant.users.index') }}">
                👤 My Users
            </a>
        @endif

        <a href="{{ route('settings.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ $active('settings.index') }}">
            ⚙️ Settings
        </a>

    </nav>

    <!-- BOTTOM SECTION (FIXED FEEL) -->
    <div class="border-t border-white/10 p-4 space-y-3">

        <!-- USER INFO -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500"></div>

            <div class="flex-1">
                <div class="text-sm font-semibold">{{ $user?->name }}</div>
                <div class="text-xs text-slate-400">{{ $user?->email }}</div>
            </div>
        </div>

        <!-- SIGN OUT (PINNED LOOK) -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button
                class="w-full text-sm py-2 rounded-xl
                       bg-red-500/10 hover:bg-red-500/20
                       text-red-300 transition">
                Sign out
            </button>
        </form>

    </div>

</aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col">

        <!-- TOPBAR -->
        <header class="sticky top-0 z-30 backdrop-blur-xl bg-white/5 border-b border-white/10">

            <div class="flex items-center justify-between px-6 py-4">

                <!-- MOBILE MENU -->
                <button class="md:hidden text-2xl" @click="sidebarOpen = !sidebarOpen">
                    ☰
                </button>

                <div>
                    <h1 class="text-lg font-semibold">Dashboard</h1>
                    <p class="text-xs text-slate-400">Welcome back 👋</p>
                </div>

                <div class="flex items-center gap-3">

                    <!-- SEARCH -->
                    <input
                        type="text"
                        placeholder="Search..."
                        class="hidden md:block px-4 py-2 rounded-xl bg-white/10 border border-white/10
                               text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none"
                    >

                    <!-- NOTIFICATION -->
                    <button class="p-2 rounded-xl bg-white/10 hover:bg-white/20 transition">
                        🔔
                    </button>

                </div>

            </div>

        </header>

        <!-- CONTENT -->
        <main class="flex-1 p-6">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000",
    };

    @if(Session::has('success'))
        toastr.success("{{ Session::get('success') }}");
    @endif

    @if(Session::has('error'))
        toastr.error("{{ Session::get('error') }}");
    @endif

    @if(Session::has('info'))
        toastr.info("{{ Session::get('info') }}");
    @endif

    @if(Session::has('warning'))
        toastr.warning("{{ Session::get('warning') }}");
    @endif

    {{-- Handle Validation Errors --}}
    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error("{{ $error }}");
        @endforeach
    @endif
</script>

@stack('scripts')
</body>
</html>