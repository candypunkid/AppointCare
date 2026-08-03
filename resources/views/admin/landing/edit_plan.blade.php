@extends('layouts.admin')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.landing.index') }}" class="text-sm text-slate-400 hover:text-white transition mb-4 inline-block">← Back to Landing Content</a>
    <h1 class="text-2xl font-bold text-white mb-6">Edit Plan</h1>
    <form method="POST" action="{{ route('admin.landing.plans.update', $plan) }}" class="space-y-4 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Plan Name</label>
                <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Badge Text</label>
                <input type="text" name="badge_text" value="{{ old('badge_text', $plan->badge_text) }}" placeholder="e.g. Most Popular" class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Monthly Price ($)</label>
                <input type="number" step="0.01" name="monthly_price" value="{{ old('monthly_price', $plan->monthly_price) }}" required class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Yearly Price ($)</label>
                <input type="number" step="0.01" name="yearly_price" value="{{ old('yearly_price', $plan->yearly_price) }}" required class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Button Text</label>
                <input type="text" name="button_text" value="{{ old('button_text', $plan->button_text) }}" class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div class="flex items-center gap-3 pt-7">
                <input type="checkbox" name="is_popular" value="1" id="is_popular" {{ $plan->is_popular ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-cyan-500">
                <label for="is_popular" class="text-sm text-slate-300">Mark as Popular</label>
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-1">Features (one per line)</label>
            <textarea name="features" required rows="5" class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">{{ is_array(old('features')) ? implode("\n", old('features')) : old('features', implode("\n", $plan->features ?? [])) }}</textarea>
            <p class="text-xs text-slate-500 mt-1">Enter each feature on a new line.</p>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-white font-semibold text-sm hover:opacity-90 transition">Update Plan</button>
            <a href="{{ route('admin.landing.index') }}" class="px-6 py-2.5 rounded-xl bg-white/10 text-slate-300 font-semibold text-sm hover:bg-white/20 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
