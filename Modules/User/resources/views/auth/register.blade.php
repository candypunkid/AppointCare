<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Register — AppointCare</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg:       #020617;
      --accent:   #06b6d4;
      --accent2:  #8b5cf6;
      --text:     #f1f5f9;
      --muted:    #8b98b0;
      --white:    #ffffff;
      --border:   rgba(255, 255, 255, 0.08);
    }
    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Inter', sans-serif;
      line-height: 1.7;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }
    header {
      position: sticky; top: 0; z-index: 100;
      padding: 18px 0;
      background: rgba(2, 6, 23, 0.8);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .nav-inner {
      max-width: 1200px; margin: 0 auto; padding: 0 32px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .logo-icon {
      width: 38px; height: 38px; border-radius: 10px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      display: grid; place-items: center;
    }
    .logo span { font-weight: 700; font-size: 17px; color: var(--white); }
    nav { display: flex; align-items: center; gap: 28px; }
    nav a { font-size: 14px; color: var(--muted); transition: color .2s; text-decoration: none; }
    nav a:hover { color: var(--white); }
    .btn-nav {
      padding: 8px 20px; border-radius: 8px;
      background: var(--accent); color: var(--white);
      font-size: 13px; font-weight: 500; text-decoration: none;
    }
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.05'/%3E%3C/svg%3E");
      pointer-events: none;
    }
    .register-wrapper {
      position: relative;
      min-height: calc(100vh - 80px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 24px;
    }
    .blob {
      position: absolute; border-radius: 50%; filter: blur(140px); pointer-events: none; z-index: 0;
    }
    .blob-1 { width: 600px; height: 600px; background: rgba(6, 182, 212, 0.1); top: -200px; left: -100px; }
    .blob-2 { width: 500px; height: 500px; background: rgba(139, 92, 246, 0.08); bottom: -200px; right: -80px; }
  </style>
</head>
<body>
    <header>
        <div class="nav-inner">
            <a href="/" class="logo">
                <div class="logo-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <span>AppointCare</span>
            </a>
            <nav>
                <a href="/#features">Features</a>
                <a href="/#how">How it works</a>
                <a href="/#pricing">Pricing</a>
                <a href="{{ route('login') }}" class="btn-nav">Sign In</a>
            </nav>
        </div>
    </header>
    <div class="register-wrapper">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>

        <div class="relative w-full max-w-5xl z-10">
            <div class="grid w-full gap-12 lg:grid-cols-[1fr_1.25fr] lg:items-center">
                <div class="hidden space-y-8 text-center lg:block lg:text-left">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-slate-950/40 backdrop-blur-xl">
                        <span class="inline-flex rounded-full bg-cyan-500/20 px-3 py-1 text-sm font-semibold text-cyan-200 ring-1 ring-cyan-200/20">
                            Smarter booking in one place
                        </span>
                        <h1 class="mt-6 text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                            Create your account for intelligent scheduling
                        </h1>
                        <p class="mt-4 text-lg leading-8 text-slate-300">
                            Welcome to AppointCare — the AI-enhanced appointment system built for speed, clarity, and beautiful workflow control.
                        </p>
                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-3xl bg-slate-900/80 p-5">
                                <p class="text-sm uppercase tracking-[0.24em] text-cyan-300">AI reminders</p>
                                <p class="mt-2 text-sm text-slate-300">Automated notifications to keep every client on time.</p>
                            </div>
                            <div class="rounded-3xl bg-slate-900/80 p-5">
                                <p class="text-sm uppercase tracking-[0.24em] text-violet-300">Instant dashboard</p>
                                <p class="mt-2 text-sm text-slate-300">See appointment insights at a glance after login.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mx-auto w-full max-w-md rounded-3xl border border-white/10 bg-slate-900/90 p-8 shadow-2xl shadow-slate-950/50 backdrop-blur-xl">
                    <div class="mb-8 text-center">
                        <p class="text-sm uppercase tracking-[0.28em] text-slate-400">Get started</p>
                        <h2 class="mt-3 text-3xl font-semibold text-white">Register for AppointCare</h2>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-6" x-data="{ role: '{{ old('role','customer') }}' }">
                        @csrf

                        <div class="space-y-4 rounded-3xl bg-slate-950/80 p-4">
                            <label class="block text-sm font-semibold text-slate-300" for="name">Full Name</label>
                            <input id="name" name="name" type="text" autocomplete="name" required
                                value="{{ old('name') }}"
                                class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30" />
                            @error('name')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-4 rounded-3xl bg-slate-950/80 p-4">
                            <label class="block text-sm font-semibold text-slate-300" for="email">Email address</label>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                value="{{ old('email') }}"
                                class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30" />
                            @error('email')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-4 rounded-3xl bg-slate-950/80 p-4">
                            <label class="block text-sm font-semibold text-slate-300" for="password">Password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30" />
                            @error('password')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-4 rounded-3xl bg-slate-950/80 p-4">
                            <label class="block text-sm font-semibold text-slate-300" for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30" />
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-4 rounded-3xl bg-slate-950/80 p-4">
                            <label class="block text-sm font-semibold text-slate-300" for="role">Account type</label>
                            <select id="role" name="role" x-model="role"
                                class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none">
                                <option value="customer">Customer</option>
                                <option value="staff">Staff</option>
                                <option value="tenant_admin">Tenant (create new)</option>
                                <option value="super_admin">Platform admin</option>
                            </select>
                            @error('role')
                                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <template x-if="role === 'tenant_admin'">
                            <div class="space-y-4 rounded-3xl bg-slate-950/80 p-4">
                                <label class="block text-sm font-semibold text-slate-300" for="tenant_name">Business / Tenant name</label>
                                <input id="tenant_name" name="tenant_name" type="text" value="{{ old('tenant_name') }}"
                                    class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none" />
                                @error('tenant_name')
                                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                                @enderror

                                <label class="block text-sm font-semibold text-slate-300" for="tenant_domain">Tenant domain (optional)</label>
                                <input id="tenant_domain" name="tenant_domain" type="text" value="{{ old('tenant_domain') }}"
                                    placeholder="example.yourdomain.com"
                                    class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none" />
                                @error('tenant_domain')
                                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>
                        </template>

                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-500 to-violet-500 px-6 py-3 text-base font-semibold text-slate-950 transition hover:brightness-110">
                            Create account
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-slate-400">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-white hover:text-cyan-300">Sign in instead</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
