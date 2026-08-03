<?php

namespace App\Http\Controllers;

use App\Services\LandingPageService;
use App\Services\TenantThemeService;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __invoke(Request $request, LandingPageService $landingService, TenantThemeService $themeService)
    {
        $tenant = tenant();
        $theme = $themeService->getTheme($tenant);
        $landing = $landingService->getLandingData($tenant);

        $brandName = $theme['brand_name'] ?? 'AppointCare';
        $heroTitle = str_replace('{brand}', '<em>' . e($brandName) . '</em>', e($theme['hero_title']));
        $heroSubtitle = e($theme['hero_subtitle']);
        $logoPath = $theme['logo_path'];
        $cssVars = $themeService->getCssVariables($tenant);

        return view('landing', array_merge(
            compact('theme', 'brandName', 'heroTitle', 'heroSubtitle', 'logoPath', 'cssVars', 'tenant'),
            $landing
        ));
    }
}
