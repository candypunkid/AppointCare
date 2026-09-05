<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Settings\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Show settings form (single row settings system)
     */
    public function index()
    {
        $setting = Setting::first();

        return view('settings::settings.form', compact('setting'));
    }

    /**
     * Store (first time settings creation)
     */
    public function store(Request $request)
    {
        // dd($request->all());
        return $this->saveSettings($request);
    }

    /**
     * Update existing settings
     */
    public function update(Request $request, $id)
    {
        return $this->saveSettings($request, $id);
    }

    /**
     * Shared logic for store + update
     */
    private function saveSettings(Request $request, $id = null)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'contact_no' => 'nullable|string|max:50',
            'local_address' => 'nullable|string|max:255',
            'full_address' => 'nullable|string|max:255',
            'pan_vat' => 'nullable|string|max:100',
            'brief_description' => 'nullable|string',

            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'github' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',

            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',

            // FILES
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'company_favicon' => 'nullable|image|mimes:png,ico',
            'footer_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'home_bg_img' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        $setting = Setting::firstOrNew(['id' => $id]);

        // TEXT FIELDS (mass assign safe)
        $setting->fill($validated);

        // FILE UPLOADS
        $setting->company_logo = $this->uploadFile($request, 'company_logo', $setting->company_logo);
        $setting->company_favicon = $this->uploadFile($request, 'company_favicon', $setting->company_favicon);
        $setting->footer_logo = $this->uploadFile($request, 'footer_logo', $setting->footer_logo);
        $setting->home_bg_img = $this->uploadFile($request, 'home_bg_img', $setting->home_bg_img);

        $setting->save();

        return redirect()
            ->back()
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * File upload helper
     */
    private function uploadFile(Request $request, $field, $oldFile = null)
    {
        if (! $request->hasFile($field)) {
            return $oldFile;
        }

        // delete old file
        if ($oldFile && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }

        return $request->file($field)->store('settings', 'public');
    }
}
