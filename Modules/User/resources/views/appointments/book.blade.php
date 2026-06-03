@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100">
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
@endsection
