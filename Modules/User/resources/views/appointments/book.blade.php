<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Book Appointment — AppointCare</title>
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

<main class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-0 overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.15),_transparent_25%),radial-gradient(circle_at_bottom_right,_rgba(168,85,247,0.15),_transparent_20%)]\"></div>
    </div>

    <div class="relative mx-auto max-w-4xl px-6 py-12 lg:px-8">
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl"> 
                Book Your Appointment
            </h1>
            <p class="mt-4 text-lg text-slate-300">
                Fill out the form below and our AI assistant will call you to confirm your appointment instantly.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 p-4">
                <h3 class="font-semibold text-rose-300">Please fix the following errors:</h3>
                <ul class="mt-2 list-inside space-y-1 text-rose-200">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4">
                <p class="text-emerald-300">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-6 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4">
                <p class="text-amber-300">{{ session('warning') }}</p>
            </div>
        @endif

        <div class="rounded-3xl border border-white/10 bg-slate-900/90 p-8 shadow-2xl shadow-slate-950/50 backdrop-blur-xl">
            <form method="POST" action="{{ route('appointments.store') }}" class="space-y-8">
                @csrf

                <!-- Full Name -->
                <div class="space-y-3">
                    <label for="name" class="block text-sm font-semibold text-slate-300">Full Name *</label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        required
                        value="{{ old('name') }}"
                        class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                        placeholder="John Doe"
                    />
                </div>

                <!-- Email -->
                <div class="space-y-3">
                    <label for="email" class="block text-sm font-semibold text-slate-300">Email Address *</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required
                        value="{{ old('email') }}"
                        class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                        placeholder="john@example.com"
                    />
                </div>

                <!-- Phone Number -->
                <div class="space-y-3">
                    <label for="phone" class="block text-sm font-semibold text-slate-300">Phone Number *</label>
                    <input 
                        id="phone" 
                        name="phone" 
                        type="tel" 
                        required
                        value="{{ old('phone') }}"
                        class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                        placeholder="+1 (555) 123-4567"
                    />
                    <p class="text-xs text-slate-400">Include country code (e.g., +1 for USA)</p>
                </div>

                <!-- Service -->
                <div class="space-y-3">
                    <label for="service" class="block text-sm font-semibold text-slate-300">Service *</label>
                    <select 
                        id="service" 
                        name="service" 
                        required
                        class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                    >
                        <option value="">Select a service</option>
                        <option value="consultation" {{ old('service') === 'consultation' ? 'selected' : '' }}>Consultation</option>
                        <option value="appointment" {{ old('service') === 'appointment' ? 'selected' : '' }}>General Appointment</option>
                        <option value="follow-up" {{ old('service') === 'follow-up' ? 'selected' : '' }}>Follow-up</option>
                        <option value="other" {{ old('service') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Preferred Date -->
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-3">
                        <label for="preferred_date" class="block text-sm font-semibold text-slate-300">Preferred Date</label>
                        <input 
                            id="preferred_date" 
                            name="preferred_date" 
                            type="date"
                            value="{{ old('preferred_date') }}"
                            class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                        />
                    </div>

                    <!-- Preferred Time -->
                    <div class="space-y-3">
                        <label for="preferred_time" class="block text-sm font-semibold text-slate-300">Preferred Time</label>
                        <input 
                            id="preferred_time" 
                            name="preferred_time" 
                            type="time"
                            value="{{ old('preferred_time') }}"
                            class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                        />
                    </div>
                </div>

                <!-- Message -->
                <div class="space-y-3">
                    <label for="message" class="block text-sm font-semibold text-slate-300">Additional Message</label>
                    <textarea 
                        id="message" 
                        name="message" 
                        rows="4"
                        class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                        placeholder="Tell us anything that might be helpful..."
                    >{{ old('message') }}</textarea>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-500 to-violet-500 px-8 py-4 text-lg font-semibold text-slate-950 transition hover:brightness-110"
                >
                    Request Appointment & Get AI Call
                </button>

                <p class="text-center text-sm text-slate-400">
                    <span class="font-semibold text-slate-300">How it works:</span> Submit your details and our AI will call you within minutes to confirm your appointment, answer questions, or reschedule.
                </p>
            </form>
        </div>

        <!-- Benefits -->
        <div class="mt-12 grid gap-6 sm:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <div class="text-2xl">⚡</div>
                <h3 class="mt-3 font-semibold text-white">Instant Confirmation</h3>
                <p class="mt-2 text-sm text-slate-400">Get your appointment confirmed via AI call immediately.</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <div class="text-2xl">🤖</div>
                <h3 class="mt-3 font-semibold text-white">AI Powered</h3>
                <p class="mt-2 text-sm text-slate-400">Smart scheduling that adapts to your preferences.</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <div class="text-2xl">✅</div>
                <h3 class="mt-3 font-semibold text-white">Easy Changes</h3>
                <p class="mt-2 text-sm text-slate-400">Cancel or reschedule anytime during your call.</p>
            </div>
        </div>
    </div>
</div>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator,
    input[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(0.8);
    }
</style>

<footer class="py-8 text-center text-slate-400 text-sm">
    <p>&copy; {{ date('Y') }} AppointCare. All rights reserved.</p>
</footer>
</body>
</html>
