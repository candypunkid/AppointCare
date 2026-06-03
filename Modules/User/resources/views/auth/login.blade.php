@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-0 overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.2),_transparent_25%),radial-gradient(circle_at_bottom_right,_rgba(168,85,247,0.18),_transparent_20%)]"></div>
    </div>
    <div class="relative mx-auto flex min-h-screen max-w-6xl items-center px-6 py-12 lg:px-8">
        <div class="grid w-full gap-12 lg:grid-cols-[1.2fr_1fr] lg:items-center">
            <div class="space-y-8">
                <div class="max-w-xl rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-slate-950/40 backdrop-blur-xl">
                    <span class="inline-flex rounded-full bg-cyan-500/20 px-3 py-1 text-sm font-semibold text-cyan-200 ring-1 ring-cyan-200/20">
                        AI appointment intelligence
                    </span>
                    <h1 class="mt-6 text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                        Secure access for your smart scheduling hub
                    </h1>
                    <p class="mt-4 text-lg leading-8 text-slate-300">
                        Sign in to manage appointments, patients, and AI-powered scheduling workflows from one polished dashboard.
                    </p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl bg-slate-900/80 p-5">
                            <p class="text-sm uppercase tracking-[0.24em] text-cyan-300">Fast scheduling</p>
                            <p class="mt-2 text-sm text-slate-300">Real-time appointment booking and confirmations.</p>
                        </div>
                        <div class="rounded-3xl bg-slate-900/80 p-5">
                            <p class="text-sm uppercase tracking-[0.24em] text-rose-300">Secure access</p>
                            <p class="mt-2 text-sm text-slate-300">Login with confidence on a platform built for privacy.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto w-full max-w-md rounded-3xl border border-white/10 bg-slate-900/90 p-8 shadow-2xl shadow-slate-950/50 backdrop-blur-xl">
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
</div>
@endsection
