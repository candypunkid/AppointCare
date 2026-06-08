<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white antialiased">

    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

        <!-- MOBILE OVERLAY -->
        <div
            x-show="sidebarOpen"
            class="fixed inset-0 bg-black/60 z-40 lg:hidden"
            @click="sidebarOpen=false"
        ></div>

        <!-- SIDEBAR -->
        <aside
            class="fixed lg:static z-50 w-72 h-full bg-white/5 backdrop-blur-2xl border-r border-white/10
                   transform transition-transform duration-300"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >

            <div class="p-6 border-b border-white/10">
                <h1 class="text-xl font-bold tracking-tight bg-gradient-to-r from-white to-indigo-300 bg-clip-text text-transparent">
                    {{ config('app.name', 'Admin Panel') }}
                </h1>
                <p class="text-xs text-slate-400 mt-1">System Control Center</p>
            </div>

            <nav class="p-4 space-y-2">

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/10 text-white shadow-lg">
                    🏠 Dashboard
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10 transition">
                    ⚙️ Settings
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10 transition">
                    👤 Users
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10 transition">
                    📊 Analytics
                </a>

            </nav>

        </aside>

        <!-- MAIN AREA -->
        <div class="flex-1 flex flex-col">

            <!-- TOPBAR -->
            <header class="sticky top-0 z-30 backdrop-blur-xl bg-white/5 border-b border-white/10">

                <div class="flex items-center justify-between px-6 py-4">

                    <!-- MOBILE BUTTON -->
                    <button
                        class="lg:hidden text-white text-2xl"
                        @click="sidebarOpen = !sidebarOpen"
                    >
                        ☰
                    </button>

                    <div class="hidden md:block">
                        <p class="text-sm text-slate-400">Welcome back</p>
                        <h2 class="text-lg font-semibold text-white">
                            Admin Dashboard
                        </h2>
                    </div>

                    <div class="flex items-center gap-4">

                        <!-- SEARCH -->
                        <div class="hidden md:block">
                            <input
                                type="text"
                                placeholder="Search..."
                                class="px-4 py-2 rounded-xl bg-white/10 border border-white/10
                                       text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none"
                            >
                        </div>

                        <!-- PROFILE -->
                        <div class="flex items-center gap-3 bg-white/10 px-3 py-2 rounded-xl">
                            <div class="w-8 h-8 rounded-full bg-indigo-500"></div>
                            <span class="text-sm text-white">Admin</span>
                        </div>

                    </div>

                </div>

            </header>

            <!-- PAGE CONTENT -->
            <main class="flex-1 p-6">

                <div class="max-w-7xl mx-auto">

                    @yield('content')

                </div>

            </main>

        </div>
    </div>

</body>
</html>