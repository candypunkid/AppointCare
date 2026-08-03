@extends('layouts.admin')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.landing.index') }}" class="text-sm text-slate-400 hover:text-white transition mb-4 inline-block">← Back to Landing Content</a>
    <h1 class="text-2xl font-bold text-white mb-6">Edit Step</h1>
    <form method="POST" action="{{ route('admin.landing.steps.update', $step) }}" class="space-y-4 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-1">Step Number</label>
            <input type="number" name="step_number" value="{{ old('step_number', $step->step_number) }}" min="1" max="10" required class="w-24 rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title', $step->title) }}" required class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-1">Description</label>
            <textarea name="description" required rows="3" class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">{{ old('description', $step->description) }}</textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-white font-semibold text-sm hover:opacity-90 transition">Update Step</button>
            <a href="{{ route('admin.landing.index') }}" class="px-6 py-2.5 rounded-xl bg-white/10 text-slate-300 font-semibold text-sm hover:bg-white/20 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
