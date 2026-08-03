@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Landing Page Content</h1>
            <p class="text-sm text-slate-400 mt-1">Manage all dynamic sections of the public landing page</p>
        </div>
        <a href="{{ route('home') }}" target="_blank"
           class="px-5 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white font-semibold text-sm hover:bg-white/20 transition">
            View Landing Page →
        </a>
    </div>

    {{-- Theme / Hero Settings --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
        <h2 class="text-lg font-bold text-white mb-4">🎨 Theme & Hero Settings</h2>
        <form method="POST" action="{{ route('admin.landing.theme') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Primary Color</label>
                <input type="color" name="primary_color" value="{{ $theme['primary_color'] }}"
                       class="w-full h-10 rounded-xl border border-white/10 bg-slate-900/90 cursor-pointer">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Secondary Color</label>
                <input type="color" name="secondary_color" value="{{ $theme['secondary_color'] }}"
                       class="w-full h-10 rounded-xl border border-white/10 bg-slate-900/90 cursor-pointer">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Accent Color</label>
                <input type="color" name="accent_color" value="{{ $theme['accent_color'] }}"
                       class="w-full h-10 rounded-xl border border-white/10 bg-slate-900/90 cursor-pointer">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Hero Title <span class="text-slate-500">(use {brand} for name)</span></label>
                <input type="text" name="hero_title" value="{{ $theme['hero_title'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Hero Subtitle</label>
                <input type="text" name="hero_subtitle" value="{{ $theme['hero_subtitle'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Hero Badge</label>
                <input type="text" name="hero_badge" value="{{ $theme['hero_badge'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Hero Button (Primary)</label>
                <input type="text" name="hero_btn_primary" value="{{ $theme['hero_btn_primary'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Hero Button (Secondary)</label>
                <input type="text" name="hero_btn_secondary" value="{{ $theme['hero_btn_secondary'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">CTA Title</label>
                <input type="text" name="cta_title" value="{{ $theme['cta_title'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">CTA Subtitle</label>
                <input type="text" name="cta_subtitle" value="{{ $theme['cta_subtitle'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">CTA Button (Primary)</label>
                <input type="text" name="cta_btn_primary" value="{{ $theme['cta_btn_primary'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">CTA Button (Secondary)</label>
                <input type="text" name="cta_btn_secondary" value="{{ $theme['cta_btn_secondary'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Footer Text</label>
                <input type="text" name="footer_text" value="{{ $theme['footer_text'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Contact Phone</label>
                <input type="text" name="contact_phone" value="{{ $theme['contact_phone'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Contact Email</label>
                <input type="email" name="contact_email" value="{{ $theme['contact_email'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-sm font-semibold text-slate-300 mb-1">Contact Address</label>
                <input type="text" name="contact_address" value="{{ $theme['contact_address'] }}"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-white font-semibold text-sm hover:opacity-90 transition">
                    Save Theme Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Features --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">✨ Features ({{ $features->count() }})</h2>
            <button onclick="document.getElementById('feature-form').classList.toggle('hidden')"
                    class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 text-white text-xs font-semibold">
                + Add Feature
            </button>
        </div>
        <form id="feature-form" method="POST" action="{{ route('admin.landing.features.store') }}" class="hidden p-4 border-b border-white/10 bg-slate-900/50">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="icon" placeholder="Icon (emoji)" class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <input type="text" name="title" placeholder="Title" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <input type="text" name="description" placeholder="Description" required class="md:col-span-2 rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-400 text-sm font-semibold hover:bg-emerald-500/30 transition">Create</button>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/10 text-slate-400"><th class="text-left px-5 py-4 font-medium">Icon</th><th class="text-left px-5 py-4 font-medium">Title</th><th class="text-left px-5 py-4 font-medium">Description</th><th class="text-center px-5 py-4 font-medium">Order</th><th class="text-right px-5 py-4 font-medium">Actions</th></tr></thead>
                <tbody>
                    @forelse($features as $feature)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="px-5 py-4 text-2xl">{{ $feature->icon }}</td>
                        <td class="px-5 py-4 text-white font-medium">{{ $feature->title }}</td>
                        <td class="px-5 py-4 text-slate-300 max-w-xs truncate">{{ $feature->description }}</td>
                        <td class="px-5 py-4 text-center text-slate-300">{{ $feature->sort_order }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.landing.features.edit', $feature) }}" class="px-3 py-1 rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.landing.features.destroy', $feature) }}" class="inline" onsubmit="return confirm('Delete this feature?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-slate-500">No features yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- How It Works Steps --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">📋 How It Works ({{ $steps->count() }})</h2>
            <button onclick="document.getElementById('step-form').classList.toggle('hidden')"
                    class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 text-white text-xs font-semibold">
                + Add Step
            </button>
        </div>
        <form id="step-form" method="POST" action="{{ route('admin.landing.steps.store') }}" class="hidden p-4 border-b border-white/10 bg-slate-900/50">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="number" name="step_number" placeholder="Step #" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <input type="text" name="title" placeholder="Title" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <input type="text" name="description" placeholder="Description" required class="md:col-span-2 rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-400 text-sm font-semibold hover:bg-emerald-500/30 transition">Create</button>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/10 text-slate-400"><th class="text-left px-5 py-4 font-medium">#</th><th class="text-left px-5 py-4 font-medium">Title</th><th class="text-left px-5 py-4 font-medium">Description</th><th class="text-right px-5 py-4 font-medium">Actions</th></tr></thead>
                <tbody>
                    @forelse($steps as $step)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="px-5 py-4 text-white font-bold">{{ $step->step_number }}</td>
                        <td class="px-5 py-4 text-white font-medium">{{ $step->title }}</td>
                        <td class="px-5 py-4 text-slate-300 max-w-xs truncate">{{ $step->description }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.landing.steps.edit', $step) }}" class="px-3 py-1 rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.landing.steps.destroy', $step) }}" class="inline" onsubmit="return confirm('Delete this step?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-slate-500">No steps yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Industries --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">🏢 Industries ({{ $industries->count() }})</h2>
            <button onclick="document.getElementById('industry-form').classList.toggle('hidden')"
                    class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 text-white text-xs font-semibold">
                + Add Industry
            </button>
        </div>
        <form id="industry-form" method="POST" action="{{ route('admin.landing.industries.store') }}" class="hidden p-4 border-b border-white/10 bg-slate-900/50">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="icon" placeholder="Icon (emoji)" class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <input type="text" name="name" placeholder="Name" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-400 text-sm font-semibold hover:bg-emerald-500/30 transition">Create</button>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/10 text-slate-400"><th class="text-left px-5 py-4 font-medium">Icon</th><th class="text-left px-5 py-4 font-medium">Name</th><th class="text-right px-5 py-4 font-medium">Actions</th></tr></thead>
                <tbody>
                    @forelse($industries as $industry)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="px-5 py-4 text-2xl">{{ $industry->icon }}</td>
                        <td class="px-5 py-4 text-white font-medium">{{ $industry->name }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.landing.industries.edit', $industry) }}" class="px-3 py-1 rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.landing.industries.destroy', $industry) }}" class="inline" onsubmit="return confirm('Delete this industry?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-12 text-center text-slate-500">No industries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pricing Plans --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">💎 Pricing Plans ({{ $plans->count() }})</h2>
            <button onclick="document.getElementById('plan-form').classList.toggle('hidden')"
                    class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 text-white text-xs font-semibold">
                + Add Plan
            </button>
        </div>
        <form id="plan-form" method="POST" action="{{ route('admin.landing.plans.store') }}" class="hidden p-4 border-b border-white/10 bg-slate-900/50">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="name" placeholder="Plan Name" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <input type="number" step="0.01" name="monthly_price" placeholder="Monthly $" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <input type="number" step="0.01" name="yearly_price" placeholder="Yearly $" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-400 text-sm font-semibold hover:bg-emerald-500/30 transition">Create</button>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/10 text-slate-400"><th class="text-left px-5 py-4 font-medium">Name</th><th class="text-left px-5 py-4 font-medium">Monthly</th><th class="text-left px-5 py-4 font-medium">Yearly</th><th class="text-center px-5 py-4 font-medium">Popular</th><th class="text-right px-5 py-4 font-medium">Actions</th></tr></thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="px-5 py-4 text-white font-medium">{{ $plan->name }}</td>
                        <td class="px-5 py-4 text-slate-300">${{ number_format($plan->monthly_price, 2) }}</td>
                        <td class="px-5 py-4 text-slate-300">${{ number_format($plan->yearly_price, 2) }}</td>
                        <td class="px-5 py-4 text-center">
                            @if($plan->is_popular)
                                <span class="px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-400 text-xs font-semibold">Popular</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.landing.plans.edit', $plan) }}" class="px-3 py-1 rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.landing.plans.destroy', $plan) }}" class="inline" onsubmit="return confirm('Delete this plan?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-slate-500">No plans yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Testimonials --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">⭐ Testimonials ({{ $testimonials->count() }})</h2>
            <button onclick="document.getElementById('testimonial-form').classList.toggle('hidden')"
                    class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 text-white text-xs font-semibold">
                + Add Testimonial
            </button>
        </div>
        <form id="testimonial-form" method="POST" action="{{ route('admin.landing.testimonials.store') }}" class="hidden p-4 border-b border-white/10 bg-slate-900/50">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="author_name" placeholder="Author Name" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <input type="text" name="author_role" placeholder="Author Role" class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <input type="number" name="rating" min="1" max="5" placeholder="Rating (1-5)" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-400 text-sm font-semibold hover:bg-emerald-500/30 transition">Create</button>
            </div>
            <textarea name="text" placeholder="Testimonial text..." required class="mt-3 w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400" rows="2"></textarea>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/10 text-slate-400"><th class="text-left px-5 py-4 font-medium">Author</th><th class="text-left px-5 py-4 font-medium">Role</th><th class="text-center px-5 py-4 font-medium">Rating</th><th class="text-left px-5 py-4 font-medium">Text</th><th class="text-right px-5 py-4 font-medium">Actions</th></tr></thead>
                <tbody>
                    @forelse($testimonials as $t)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="px-5 py-4 text-white font-medium">{{ $t->author_name }}</td>
                        <td class="px-5 py-4 text-slate-300">{{ $t->author_role ?? '—' }}</td>
                        <td class="px-5 py-4 text-center text-amber-400">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</td>
                        <td class="px-5 py-4 text-slate-300 max-w-xs truncate">{{ $t->text }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.landing.testimonials.edit', $t) }}" class="px-3 py-1 rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.landing.testimonials.destroy', $t) }}" class="inline" onsubmit="return confirm('Delete this testimonial?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-slate-500">No testimonials yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FAQs --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">❓ FAQs ({{ $faqs->count() }})</h2>
            <button onclick="document.getElementById('faq-form').classList.toggle('hidden')"
                    class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 text-white text-xs font-semibold">
                + Add FAQ
            </button>
        </div>
        <form id="faq-form" method="POST" action="{{ route('admin.landing.faqs.store') }}" class="hidden p-4 border-b border-white/10 bg-slate-900/50">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="question" placeholder="Question" required class="md:col-span-2 rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-400 text-sm font-semibold hover:bg-emerald-500/30 transition">Create</button>
            </div>
            <textarea name="answer" placeholder="Answer..." required class="mt-3 w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400" rows="2"></textarea>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/10 text-slate-400"><th class="text-left px-5 py-4 font-medium">Question</th><th class="text-left px-5 py-4 font-medium">Answer</th><th class="text-right px-5 py-4 font-medium">Actions</th></tr></thead>
                <tbody>
                    @forelse($faqs as $faq)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="px-5 py-4 text-white font-medium max-w-xs">{{ $faq->question }}</td>
                        <td class="px-5 py-4 text-slate-300 max-w-sm truncate">{{ $faq->answer }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.landing.faqs.edit', $faq) }}" class="px-3 py-1 rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.landing.faqs.destroy', $faq) }}" class="inline" onsubmit="return confirm('Delete this FAQ?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-12 text-center text-slate-500">No FAQs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Stats --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">📊 Stats ({{ $stats->count() }})</h2>
            <button onclick="document.getElementById('stat-form').classList.toggle('hidden')"
                    class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 text-white text-xs font-semibold">
                + Add Stat
            </button>
        </div>
        <form id="stat-form" method="POST" action="{{ route('admin.landing.stats.store') }}" class="hidden p-4 border-b border-white/10 bg-slate-900/50">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="label" placeholder="Label" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <input type="text" name="value" placeholder="Value (e.g. 98%)" required class="rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2 text-white text-sm outline-none focus:border-cyan-400">
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-400 text-sm font-semibold hover:bg-emerald-500/30 transition">Create</button>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/10 text-slate-400"><th class="text-left px-5 py-4 font-medium">Label</th><th class="text-left px-5 py-4 font-medium">Value</th><th class="text-right px-5 py-4 font-medium">Actions</th></tr></thead>
                <tbody>
                    @forelse($stats as $stat)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="px-5 py-4 text-white font-medium">{{ $stat->label }}</td>
                        <td class="px-5 py-4 text-slate-300 font-bold">{{ $stat->value }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.landing.stats.edit', $stat) }}" class="px-3 py-1 rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.landing.stats.destroy', $stat) }}" class="inline" onsubmit="return confirm('Delete this stat?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-12 text-center text-slate-500">No stats yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
