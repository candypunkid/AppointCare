@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white">

<div x-data="{ tab: 'general' }" class="max-w-7xl mx-auto px-6 py-10">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">

        <div>
            <h1 class="text-4xl font-bold tracking-tight bg-gradient-to-r from-white to-indigo-300 bg-clip-text text-transparent">
                System Settings
            </h1>
            <p class="text-slate-400 mt-2">
                Manage company profile, branding, SEO and system configuration
            </p>
        </div>

        <button
            type="submit"
            form="settingsForm"
            class="px-6 py-3 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-400 hover:to-purple-500
                   text-white font-semibold shadow-xl shadow-indigo-500/20 transition-all duration-300 hover:scale-[1.02]"
        >
            Save Changes
        </button>

    </div>

    <!-- FORM -->
    <form
        id="settingsForm"
        action="{{ isset($setting) ? route('settings.update',$setting->id) : route('settings.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf
        @if(isset($setting))
            @method('PUT')
        @endif

        <!-- TAB NAV -->
        <div class="sticky top-4 z-10 backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-2 flex flex-wrap gap-2 shadow-xl">

            @foreach([
                'general' => '🏢 General',
                'contact' => '📞 Contact',
                'social' => '🌐 Social',
                'seo' => '🔍 SEO',
                'branding' => '🎨 Branding'
            ] as $key => $label)

            <button
                type="button"
                @click="tab='{{ $key }}'"
                class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-300"
                :class="tab==='{{ $key }}'
                    ? 'bg-white text-slate-900 shadow-lg'
                    : 'text-slate-300 hover:bg-white/10'"
            >
                {{ $label }}
            </button>

            @endforeach

        </div>

        <!-- CARD -->
        <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-2xl shadow-2xl p-8">

            <!-- GENERAL -->
            <div x-show="tab==='general'" x-transition class="grid md:grid-cols-2 gap-6">

                <div class="space-y-1">
                    <label class="text-sm text-slate-300">Company Name</label>
                    <input type="text" name="company_name"
                        value="{{ old('company_name',$setting->company_name ?? '') }}"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10
                               focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="Acme Corporation">
                </div>

                <div class="space-y-1">
                    <label class="text-sm text-slate-300">PAN / VAT</label>
                    <input type="text" name="pan_vat"
                        value="{{ old('pan_vat',$setting->pan_vat ?? '') }}"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10
                               focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="PAN / VAT">
                </div>

                <div class="md:col-span-2 space-y-1">
                    <label class="text-sm text-slate-300">Description</label>
                    <textarea name="brief_description" rows="4"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10
                               focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="Write something about your company...">{{ old('brief_description',$setting->brief_description ?? '') }}</textarea>
                </div>

            </div>

            <!-- CONTACT -->
            <div x-show="tab==='contact'" x-transition class="grid md:grid-cols-2 gap-6">

                @foreach([
                    'email' => 'Email',
                    'phone' => 'Phone',
                    'contact_no' => 'Contact Number',
                    'local_address' => 'Local Address'
                ] as $name => $label)

                <div class="space-y-1">
                    <label class="text-sm text-slate-300">{{ $label }}</label>
                    <input type="text" name="{{ $name }}"
                        value="{{ old($name,$setting->$name ?? '') }}"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10
                               focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="{{ $label }}">
                </div>

                @endforeach

                <div class="md:col-span-2 space-y-1">
                    <label class="text-sm text-slate-300">Full Address</label>
                    <input type="text" name="full_address"
                        value="{{ old('full_address',$setting->full_address ?? '') }}"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10
                               focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="Full Address">
                </div>

            </div>

            <!-- SOCIAL -->
            <div x-show="tab==='social'" x-transition class="grid md:grid-cols-2 gap-6">

                @foreach(['facebook','instagram','youtube','twitter','linkedin','github'] as $social)
                <div class="space-y-1">
                    <label class="text-sm text-slate-300 capitalize">{{ $social }}</label>
                    <input type="text" name="{{ $social }}"
                        value="{{ old($social,$setting->$social ?? '') }}"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10
                               focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="{{ ucfirst($social) }} URL">
                </div>
                @endforeach

            </div>

            <!-- SEO -->
            <div x-show="tab==='seo'" x-transition class="grid md:grid-cols-2 gap-6">

                <div class="space-y-1">
                    <label class="text-sm text-slate-300">Meta Title</label>
                    <input type="text" name="meta_title"
                        value="{{ old('meta_title',$setting->meta_title ?? '') }}"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10
                               focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="Meta Title">
                </div>

                <div class="space-y-1">
                    <label class="text-sm text-slate-300">Keywords</label>
                    <input type="text" name="meta_keywords"
                        value="{{ old('meta_keywords',$setting->meta_keywords ?? '') }}"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10
                               focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="SEO Keywords">
                </div>

                <div class="md:col-span-2 space-y-1">
                    <label class="text-sm text-slate-300">Meta Description</label>
                    <textarea name="meta_description" rows="4"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10
                               focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="Meta description...">{{ old('meta_description',$setting->meta_description ?? '') }}</textarea>
                </div>

            </div>

            <!-- BRANDING -->
            <div x-show="tab==='branding'" x-transition class="grid md:grid-cols-2 gap-6">

                @foreach([
                    'company_logo' => 'Company Logo',
                    'company_favicon' => 'Favicon',
                    'footer_logo' => 'Footer Logo',
                    'home_bg_img' => 'Home Background'
                ] as $name => $label)

                <div class="space-y-2">
                    <label class="text-sm text-slate-300">{{ $label }}</label>

                    <div class="border border-dashed border-white/20 rounded-xl p-5 bg-slate-900/40 hover:bg-slate-900/60 transition">
                        <input type="file" name="{{ $name }}" class="text-sm text-slate-300">
                    </div>
                </div>

                @endforeach

            </div>

        </div>
    </form>

</div>

</div>

@push('scripts')
<script>
    /**
     * Automatically switch to the tab containing validation errors.
     * This ensures the user immediately sees the field that triggered the Toastr error.
     */
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            @php
                $firstError = $errors->keys()[0];
                $tabMap = [
                    'company_name' => 'general', 'pan_vat' => 'general', 'brief_description' => 'general',
                    'email' => 'contact', 'phone' => 'contact', 'contact_no' => 'contact', 'local_address' => 'contact', 'full_address' => 'contact',
                    'facebook' => 'social', 'instagram' => 'social', 'youtube' => 'social', 'twitter' => 'social', 'linkedin' => 'social', 'github' => 'social',
                    'meta_title' => 'seo', 'meta_keywords' => 'seo', 'meta_description' => 'seo',
                    'company_logo' => 'branding', 'company_favicon' => 'branding', 'footer_logo' => 'branding', 'home_bg_img' => 'branding'
                ];
                $targetTab = $tabMap[$firstError] ?? 'general';
            @endphp

            // Access the Alpine.js component data to switch tabs
            const settingsContainer = document.querySelector('[x-data]');
            if (settingsContainer && window.Alpine) {
                Alpine.$data(settingsContainer).tab = '{{ $targetTab }}';
            }
        @endif
    });
</script>
@endpush

@endsection