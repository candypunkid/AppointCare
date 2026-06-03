@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-0 overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(16,185,129,0.16),_transparent_20%),radial-gradient(circle_at_bottom_left,_rgba(234,179,8,0.14),_transparent_18%)]"></div>
    </div>

    <div class="relative mx-auto flex min-h-screen max-w-6xl items-center px-6 py-12 lg:px-8">
        <div class="grid w-full gap-12 lg:grid-cols-[1fr_1.25fr] lg:items-center">
            <div class="space-y-8 text-center lg:text-left">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-slate-950/40 backdrop-blur-xl">
                    <span class="inline-flex rounded-full bg-emerald-500/20 px-3 py-1 text-sm font-semibold text-emerald-200 ring-1 ring-emerald-200/20">
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
                            <p class="text-sm uppercase tracking-[0.24em] text-emerald-300">AI reminders</p>
                            <p class="mt-2 text-sm text-slate-300">Automated notifications to keep every client on time.</p>
                        </div>
                        <div class="rounded-3xl bg-slate-900/80 p-5">
                            <p class="text-sm uppercase tracking-[0.24em] text-amber-300">Instant dashboard</p>
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
                            class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30" />
                        @error('name')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-4 rounded-3xl bg-slate-950/80 p-4">
                        <label class="block text-sm font-semibold text-slate-300" for="email">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email') }}"
                            class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30" />
                        @error('email')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-4 rounded-3xl bg-slate-950/80 p-4">
                        <label class="block text-sm font-semibold text-slate-300" for="password">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30" />
                        @error('password')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-4 rounded-3xl bg-slate-950/80 p-4">
                        <label class="block text-sm font-semibold text-slate-300" for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                            class="w-full rounded-2xl border border-slate-700 bg-slate-900/90 px-4 py-3 text-slate-100 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30" />
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
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-500 to-cyan-400 px-6 py-3 text-base font-semibold text-slate-950 transition hover:brightness-110">
                        Create account
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-400">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-white hover:text-emerald-300">Sign in instead</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
