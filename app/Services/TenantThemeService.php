<?php

namespace App\Services;

use App\Models\Tenant;

class TenantThemeService
{
    public function getTheme(?Tenant $tenant): array
    {
        $defaults = $this->defaults();

        if (! $tenant) {
            return $defaults;
        }

        $settings = $tenant->settings ?? [];

        return [
            'primary_color' => $settings['theme_primary'] ?? $defaults['primary_color'],
            'secondary_color' => $settings['theme_secondary'] ?? $defaults['secondary_color'],
            'accent_color' => $settings['theme_accent'] ?? $defaults['accent_color'],
            'bg_color' => $settings['theme_bg'] ?? $defaults['bg_color'],
            'bg2_color' => $settings['theme_bg2'] ?? $defaults['bg2_color'],
            'text_color' => $settings['theme_text'] ?? $defaults['text_color'],
            'card_color' => $settings['theme_card'] ?? $defaults['card_color'],
            'border_color' => $settings['theme_border'] ?? $defaults['border_color'],
            'logo_path' => $tenant->logo_path,
            'brand_name' => $tenant->name,
            'hero_title' => $settings['hero_title'] ?? $defaults['hero_title'],
            'hero_subtitle' => $settings['hero_subtitle'] ?? $defaults['hero_subtitle'],
            'font_family' => $settings['font_family'] ?? $defaults['font_family'],
            'footer_text' => $settings['footer_text'] ?? $defaults['footer_text'],
            'contact_phone' => $tenant->phone ?? $defaults['contact_phone'],
            'contact_email' => $tenant->email ?? $defaults['contact_email'],
            'contact_address' => $settings['contact_address'] ?? $defaults['contact_address'],
        ];
    }

    public function getCssVariables(?Tenant $tenant): string
    {
        $theme = $this->getTheme($tenant);

        return "
--primary: {$theme['primary_color']};
--secondary: {$theme['secondary_color']};
--accent: {$theme['accent_color']};
--bg: {$theme['bg_color']};
--bg2: {$theme['bg2_color']};
--text: {$theme['text_color']};
--card-bg: {$theme['card_color']};
--border-color: {$theme['border_color']};
--font-family: {$theme['font_family']};
";
    }

    public function getInlineCss(?Tenant $tenant): string
    {
        $theme = $this->getTheme($tenant);
        $primary = $theme['primary_color'];
        $accent = $theme['accent_color'];

        return "
.brand-gradient { background: linear-gradient(135deg, {$primary}, {$accent}) !important; }
.brand-text { color: {$primary} !important; }
.brand-border { border-color: {$primary} !important; }
.brand-bg { background: {$primary} !important; }
.brand-bg-light { background: color-mix(in srgb, {$primary} 10%, transparent) !important; }
";
    }

    public function defaults(): array
    {
        return [
            'primary_color' => '#06b6d4',
            'secondary_color' => '#8b5cf6',
            'accent_color' => '#5b8def',
            'bg_color' => '#020617',
            'bg2_color' => '#0f172a',
            'text_color' => '#f1f5f9',
            'card_color' => 'rgba(15, 23, 42, 0.8)',
            'border_color' => 'rgba(255, 255, 255, 0.08)',
            'logo_path' => null,
            'hero_title' => 'Simplified Appointment Management with {brand}',
            'hero_subtitle' => 'Streamline your schedule — simple, effective, stress-free.',
            'font_family' => "'Inter', sans-serif",
            'footer_text' => null,
            'contact_phone' => null,
            'contact_email' => null,
            'contact_address' => null,
        ];
    }

    public function saveTheme(Tenant $tenant, array $themeData): void
    {
        $settings = $tenant->settings ?? [];
        $mapping = [
            'theme_primary' => 'primary_color',
            'theme_secondary' => 'secondary_color',
            'theme_accent' => 'accent_color',
            'theme_bg' => 'bg_color',
            'theme_bg2' => 'bg2_color',
            'theme_text' => 'text_color',
            'theme_card' => 'card_color',
            'theme_border' => 'border_color',
        ];

        foreach ($mapping as $settingKey => $themeKey) {
            if (isset($themeData[$themeKey])) {
                $settings[$settingKey] = $themeData[$themeKey];
            }
        }

        $textFields = ['hero_title', 'hero_subtitle', 'font_family', 'footer_text', 'contact_address'];
        foreach ($textFields as $field) {
            if (isset($themeData[$field])) {
                $settings[$field] = $themeData[$field];
            }
        }

        $tenant->update(['settings' => $settings]);
    }
}
