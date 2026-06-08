<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Log In — AppointCare</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg:       #020617; /* Slate 950 */
      --accent:   #06b6d4; /* Cyan 500 */
      --accent2:  #8b5cf6; /* Violet 500 */
      --text:     #f1f5f9; /* Slate 100 */
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
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.05'/%3E%3C/svg%3E");
      pointer-events: none;
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
    .login-wrapper {
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
                <a href="/register" class="btn-nav">Get Started</a>
            </nav>
        </div>
    </header>

    <div class="login-wrapper">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        
        <div class="relative w-full max-w-md z-10">
            <div class="rounded-3xl border border-white/10 bg-slate-900/90 p-8 shadow-2xl shadow-slate-950/50 backdrop-blur-xl">
                <div class="mb-8 text-center">
                    <p class="text-sm uppercase tracking-[0.28em] text-slate-400">Welcome back</p>
                    <h2 class="mt-3 text-3xl font-semibold text-white">Log in to AppointCare</h2>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

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
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30" />
                        @error('password')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between text-sm text-slate-400">
                        <label class="inline-flex items-center gap-2">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-cyan-500 focus:ring-cyan-400/60" />
                            Remember me
                        </label>
                    </div>

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-500 to-violet-500 px-6 py-3 text-base font-semibold text-slate-950 transition hover:brightness-110">
                        Sign in
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-400">
                    New to AppointCare?
                    <a href="{{ route('register') }}" class="font-semibold text-white hover:text-cyan-300">Create an account</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
