<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TenantThemeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function __construct(
        protected TenantThemeService $themeService
    ) {}

    public function index(): View
    {
        $tenants = Tenant::withCount('users', 'appointments')->latest()->paginate(15);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('admin.tenants.create', [
            'defaults' => $this->themeService->defaults(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string|max:1000',
            'logo_path' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['settings'] = [];

        Tenant::create($validated);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant created successfully.');
    }

    public function edit(Tenant $tenant): View
    {
        $theme = $this->themeService->getTheme($tenant);
        return view('admin.tenants.edit', compact('tenant', 'theme'));
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . $tenant->id,
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenant->id,
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string|max:1000',
            'logo_path' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $tenant->update($validated);

        return redirect()->route('admin.tenants.edit', $tenant)
            ->with('success', 'Tenant updated successfully.');
    }

    public function theme(Request $request, Tenant $tenant): RedirectResponse
    {
        $themeData = $request->validate([
            'primary_color' => 'required|string|max:50',
            'secondary_color' => 'required|string|max:50',
            'accent_color' => 'required|string|max:50',
            'bg_color' => 'required|string|max:50',
            'bg2_color' => 'required|string|max:50',
            'text_color' => 'required|string|max:50',
            'card_color' => 'required|string|max:50',
            'border_color' => 'required|string|max:50',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'font_family' => 'nullable|string|max:100',
            'footer_text' => 'nullable|string|max:500',
            'contact_address' => 'nullable|string|max:500',
        ]);

        $this->themeService->saveTheme($tenant, $themeData);

        return redirect()->route('admin.tenants.edit', $tenant)
            ->with('success', 'Theme settings saved. Changes reflect on the tenant\'s landing page.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenant->delete();
        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }
}
