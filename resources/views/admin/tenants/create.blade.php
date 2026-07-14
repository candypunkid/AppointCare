@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-white">Create Tenant</h1>
        <p class="text-sm text-slate-400 mt-1">Add a new tenant organization</p>
    </div>

    <form action="{{ route('admin.tenants.store') }}" method="POST" class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Organization Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 outline-none">
            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Slug (subdomain)</label>
            <input type="text" name="slug" value="{{ old('slug') }}" required placeholder="e.g. myclinic"
                   class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 outline-none">
            <p class="text-xs text-slate-500 mt-1">Used as subdomain: <strong>myclinic</strong>.yourapp.com</p>
            @error('slug') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Custom Domain</label>
                <input type="text" name="domain" value="{{ old('domain') }}" placeholder="appointments.myclinic.com"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Logo URL (optional)</label>
                <input type="text" name="logo_path" value="{{ old('logo_path') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 outline-none">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" checked id="active"
                   class="w-4 h-4 rounded bg-white/10 border-white/20 text-cyan-500 focus:ring-cyan-500">
            <label for="active" class="text-sm text-slate-300">Active (tenant can be accessed)</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-white font-semibold text-sm hover:opacity-90 transition">
                Create Tenant
            </button>
            <a href="{{ route('admin.tenants.index') }}" class="px-6 py-2.5 rounded-xl bg-white/10 text-slate-300 hover:bg-white/20 transition text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
