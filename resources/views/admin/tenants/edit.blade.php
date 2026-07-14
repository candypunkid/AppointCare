@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    {{-- Tenant Details --}}
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $tenant->name }}</h1>
                <p class="text-sm text-slate-400 mt-1">Manage tenant details and theme</p>
            </div>
            <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST"
                  onsubmit="return confirm('Delete this tenant and all associated data?')">
                @csrf @method('DELETE')
                <button class="px-4 py-2 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500/20 transition text-sm font-medium">
                    Delete
                </button>
            </form>
        </div>

        <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST"
              class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 space-y-5 mb-8">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white focus:ring-2 focus:ring-cyan-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $tenant->slug) }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white focus:ring-2 focus:ring-cyan-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Custom Domain</label>
                    <input type="text" name="domain" value="{{ old('domain', $tenant->domain) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white focus:ring-2 focus:ring-cyan-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white focus:ring-2 focus:ring-cyan-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white focus:ring-2 focus:ring-cyan-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Logo URL</label>
                    <input type="text" name="logo_path" value="{{ old('logo_path', $tenant->logo_path) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white focus:ring-2 focus:ring-cyan-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Description</label>
                <textarea name="description" rows="2"
                          class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white focus:ring-2 focus:ring-cyan-500 outline-none">{{ old('description', $tenant->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" id="active" @checked($tenant->is_active)
                       class="w-4 h-4 rounded bg-white/10 border-white/20 text-cyan-500 focus:ring-cyan-500">
                <label for="active" class="text-sm text-slate-300">Active</label>
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-white font-semibold text-sm hover:opacity-90 transition">
                Save Details
            </button>
        </form>

        {{-- Theme Settings --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
            <h2 class="text-lg font-bold text-white mb-1">Theme & Branding</h2>
            <p class="text-sm text-slate-400 mb-6">Customize how this tenant's landing page and portal look.</p>

            <form action="{{ route('admin.tenants.theme', $tenant) }}" method="POST" id="themeForm">
                @csrf

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Primary</label>
                        <div class="flex gap-2">
                            <input type="color" name="primary_color" value="{{ $theme['primary_color'] }}"
                                   class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border border-white/10"
                                   onchange="updatePreview()">
                            <input type="text" name="primary_color" value="{{ $theme['primary_color'] }}"
                                   class="flex-1 px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none"
                                   oninput="updatePreview()">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Secondary</label>
                        <div class="flex gap-2">
                            <input type="color" name="secondary_color" value="{{ $theme['secondary_color'] }}"
                                   class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border border-white/10"
                                   onchange="updatePreview()">
                            <input type="text" name="secondary_color" value="{{ $theme['secondary_color'] }}"
                                   class="flex-1 px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none"
                                   oninput="updatePreview()">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Accent</label>
                        <div class="flex gap-2">
                            <input type="color" name="accent_color" value="{{ $theme['accent_color'] }}"
                                   class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border border-white/10"
                                   onchange="updatePreview()">
                            <input type="text" name="accent_color" value="{{ $theme['accent_color'] }}"
                                   class="flex-1 px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none"
                                   oninput="updatePreview()">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Background</label>
                        <div class="flex gap-2">
                            <input type="color" name="bg_color" value="{{ $theme['bg_color'] }}"
                                   class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border border-white/10"
                                   onchange="updatePreview()">
                            <input type="text" name="bg_color" value="{{ $theme['bg_color'] }}"
                                   class="flex-1 px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none"
                                   oninput="updatePreview()">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">BG Secondary</label>
                        <input type="text" name="bg2_color" value="{{ $theme['bg2_color'] }}"
                               class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none"
                               oninput="updatePreview()">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Text Color</label>
                        <input type="text" name="text_color" value="{{ $theme['text_color'] }}"
                               class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none"
                               oninput="updatePreview()">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Card BG</label>
                        <input type="text" name="card_color" value="{{ $theme['card_color'] }}"
                               class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none"
                               oninput="updatePreview()">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Border</label>
                        <input type="text" name="border_color" value="{{ $theme['border_color'] }}"
                               class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none"
                               oninput="updatePreview()">
                    </div>
                </div>

                <div class="border-t border-white/10 pt-6 mb-6">
                    <h3 class="text-sm font-semibold text-white mb-4">Content</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Hero Title (use {brand} for name)</label>
                            <input type="text" name="hero_title" value="{{ $theme['hero_title'] }}"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Hero Subtitle</label>
                            <input type="text" name="hero_subtitle" value="{{ $theme['hero_subtitle'] }}"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Font Family</label>
                            <input type="text" name="font_family" value="{{ $theme['font_family'] }}"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Footer Text</label>
                            <input type="text" name="footer_text" value="{{ $theme['footer_text'] }}"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-slate-400 mb-1">Contact Address</label>
                            <input type="text" name="contact_address" value="{{ $theme['contact_address'] }}"
                                   class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-xs outline-none">
                        </div>
                    </div>
                </div>

                {{-- Live Preview --}}
                <div class="border-t border-white/10 pt-6 mb-6">
                    <h3 class="text-sm font-semibold text-white mb-3">Live Preview</h3>
                    <div id="themePreview"
                         class="rounded-xl p-6 border"
                         style="background: {{ $theme['bg_color'] }}; border-color: {{ $theme['border_color'] }};">
                        <div class="flex items-center gap-3 mb-4">
                            <div id="previewLogo"
                                 class="w-10 h-10 rounded-xl grid place-items-center text-white text-xs font-bold"
                                 style="background: linear-gradient(135deg, {{ $theme['primary_color'] }}, {{ $theme['secondary_color'] }});">
                                {{ substr($tenant->name, 0, 2) }}
                            </div>
                            <span id="previewBrand" style="color: {{ $theme['text_color'] }}; font-weight: 700;">{{ $tenant->name }}</span>
                        </div>
                        <h2 id="previewTitle" class="text-xl font-bold mb-2" style="color: {{ $theme['text_color'] }};">{{ str_replace('{brand}', $tenant->name, $theme['hero_title']) }}</h2>
                        <p id="previewSub" class="text-sm mb-4" style="color: {{ $theme['text_color'] }}; opacity: 0.7;">{{ $theme['hero_subtitle'] }}</p>
                        <div class="flex gap-3">
                            <span id="previewBtn1" class="px-5 py-2 rounded-xl text-white text-sm font-semibold"
                                  style="background: linear-gradient(135deg, {{ $theme['primary_color'] }}, {{ $theme['secondary_color'] }});">
                                Get Started
                            </span>
                            <span id="previewBtn2" class="px-5 py-2 rounded-xl text-sm font-semibold"
                                  style="border: 1px solid {{ $theme['border_color'] }}; color: {{ $theme['text_color'] }};">
                                Learn More
                            </span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-white font-semibold text-sm hover:opacity-90 transition">
                    Save Theme
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updatePreview() {
    const form = document.getElementById('themeForm');
    const get = (name) => form.querySelector(`[name="${name}"]`)?.value || '';

    const primary = get('primary_color');
    const secondary = get('secondary_color');
    const bg = get('bg_color');
    const border = get('border_color');
    const text = get('text_color');
    const title = get('hero_title');
    const subtitle = get('hero_subtitle');
    const brand = '{{ $tenant->name }}';

    const preview = document.getElementById('themePreview');
    preview.style.background = bg;
    preview.style.borderColor = border;

    document.getElementById('previewLogo').style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
    document.getElementById('previewBrand').style.color = text;
    document.getElementById('previewBrand').textContent = brand;
    document.getElementById('previewTitle').style.color = text;
    document.getElementById('previewTitle').textContent = title.replace('{brand}', brand);
    document.getElementById('previewSub').style.color = text;
    document.getElementById('previewSub').textContent = subtitle;
    document.getElementById('previewBtn1').style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
    document.getElementById('previewBtn2').style.borderColor = border;
    document.getElementById('previewBtn2').style.color = text;
}
</script>
@endpush
@endsection
