@extends('layouts.admin')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.landing.index') }}" class="text-sm text-slate-400 hover:text-white transition mb-4 inline-block">← Back to Landing Content</a>
    <h1 class="text-2xl font-bold text-white mb-6">Edit Testimonial</h1>
    <form method="POST" action="{{ route('admin.landing.testimonials.update', $testimonial) }}" class="space-y-4 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Author Name</label>
                <input type="text" name="author_name" value="{{ old('author_name', $testimonial->author_name) }}" required class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Author Role</label>
                <input type="text" name="author_role" value="{{ old('author_role', $testimonial->author_role) }}" class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-1">Rating (1-5)</label>
                <input type="number" name="rating" value="{{ old('rating', $testimonial->rating) }}" min="1" max="5" required class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-1">Testimonial Text</label>
            <textarea name="text" required rows="4" class="w-full rounded-xl border border-white/10 bg-slate-900/90 px-4 py-2.5 text-white text-sm outline-none focus:border-cyan-400">{{ old('text', $testimonial->text) }}</textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-white font-semibold text-sm hover:opacity-90 transition">Update Testimonial</button>
            <a href="{{ route('admin.landing.index') }}" class="px-6 py-2.5 rounded-xl bg-white/10 text-slate-300 font-semibold text-sm hover:bg-white/20 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
