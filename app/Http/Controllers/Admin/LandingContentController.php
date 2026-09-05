<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingHowStep;
use App\Models\LandingIndustry;
use App\Models\LandingPlan;
use App\Models\LandingStat;
use App\Models\LandingTestimonial;
use App\Services\TenantThemeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingContentController extends Controller
{
    public function __construct(
        protected TenantThemeService $themeService
    ) {}

    public function index(): View
    {
        return view('admin.landing.index', [
            'features' => LandingFeature::whereNull('tenant_id')->orderBy('sort_order')->get(),
            'steps' => LandingHowStep::whereNull('tenant_id')->orderBy('sort_order')->get(),
            'industries' => LandingIndustry::whereNull('tenant_id')->orderBy('sort_order')->get(),
            'plans' => LandingPlan::whereNull('tenant_id')->orderBy('sort_order')->get(),
            'testimonials' => LandingTestimonial::whereNull('tenant_id')->orderBy('sort_order')->get(),
            'faqs' => LandingFaq::whereNull('tenant_id')->orderBy('sort_order')->get(),
            'stats' => LandingStat::whereNull('tenant_id')->orderBy('sort_order')->get(),
            'theme' => $this->themeService->defaults(),
        ]);
    }

    // ─── Theme / Hero / CTA ─────────────────────────────────

    public function updateTheme(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'primary_color' => 'required|string|max:50',
            'secondary_color' => 'required|string|max:50',
            'accent_color' => 'required|string|max:50',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_badge' => 'nullable|string|max:255',
            'hero_btn_primary' => 'nullable|string|max:100',
            'hero_btn_secondary' => 'nullable|string|max:100',
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string|max:500',
            'cta_btn_primary' => 'nullable|string|max:100',
            'cta_btn_secondary' => 'nullable|string|max:100',
            'footer_text' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string|max:500',
        ]);

        $tenant = tenant();
        if ($tenant) {
            $this->themeService->saveTheme($tenant, $data);
        } else {
            session()->put('landing_theme', $data);
        }

        return redirect()->route('admin.landing.index')
            ->with('success', 'Landing page theme updated successfully.');
    }

    // ─── Features ────────────────────────────────────────────

    public function storeFeature(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'icon' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? LandingFeature::max('sort_order') + 1;

        LandingFeature::create($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Feature created successfully.');
    }

    public function editFeature(LandingFeature $feature): View
    {
        return view('admin.landing.edit_feature', compact('feature'));
    }

    public function updateFeature(Request $request, LandingFeature $feature): RedirectResponse
    {
        $data = $request->validate([
            'icon' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $feature->update($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Feature updated successfully.');
    }

    public function destroyFeature(LandingFeature $feature): RedirectResponse
    {
        $feature->delete();

        return redirect()->route('admin.landing.index')
            ->with('success', 'Feature deleted successfully.');
    }

    // ─── How It Works Steps ──────────────────────────────────

    public function storeStep(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'step_number' => 'required|integer|min:1|max:10',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? LandingHowStep::max('sort_order') + 1;

        LandingHowStep::create($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Step created successfully.');
    }

    public function editStep(LandingHowStep $step): View
    {
        return view('admin.landing.edit_step', compact('step'));
    }

    public function updateStep(Request $request, LandingHowStep $step): RedirectResponse
    {
        $data = $request->validate([
            'step_number' => 'required|integer|min:1|max:10',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $step->update($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Step updated successfully.');
    }

    public function destroyStep(LandingHowStep $step): RedirectResponse
    {
        $step->delete();

        return redirect()->route('admin.landing.index')
            ->with('success', 'Step deleted successfully.');
    }

    // ─── Industries ──────────────────────────────────────────

    public function storeIndustry(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'icon' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? LandingIndustry::max('sort_order') + 1;

        LandingIndustry::create($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Industry created successfully.');
    }

    public function editIndustry(LandingIndustry $industry): View
    {
        return view('admin.landing.edit_industry', compact('industry'));
    }

    public function updateIndustry(Request $request, LandingIndustry $industry): RedirectResponse
    {
        $data = $request->validate([
            'icon' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $industry->update($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Industry updated successfully.');
    }

    public function destroyIndustry(LandingIndustry $industry): RedirectResponse
    {
        $industry->delete();

        return redirect()->route('admin.landing.index')
            ->with('success', 'Industry deleted successfully.');
    }

    // ─── Plans ───────────────────────────────────────────────

    public function storePlan(Request $request): RedirectResponse
    {
        $request->merge([
            'features' => is_string($request->features)
                ? array_filter(array_map('trim', explode("\n", $request->features)))
                : $request->features,
        ]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'is_popular' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'button_text' => 'nullable|string|max:100',
            'badge_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_popular'] = $request->boolean('is_popular');
        $data['sort_order'] = $data['sort_order'] ?? LandingPlan::max('sort_order') + 1;

        LandingPlan::create($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Plan created successfully.');
    }

    public function editPlan(LandingPlan $plan): View
    {
        return view('admin.landing.edit_plan', compact('plan'));
    }

    public function updatePlan(Request $request, LandingPlan $plan): RedirectResponse
    {
        $request->merge([
            'features' => is_string($request->features)
                ? array_filter(array_map('trim', explode("\n", $request->features)))
                : $request->features,
        ]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'is_popular' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'button_text' => 'nullable|string|max:100',
            'badge_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_popular'] = $request->boolean('is_popular');

        $plan->update($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Plan updated successfully.');
    }

    public function destroyPlan(LandingPlan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route('admin.landing.index')
            ->with('success', 'Plan deleted successfully.');
    }

    // ─── Testimonials ────────────────────────────────────────

    public function storeTestimonial(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'text' => 'required|string|max:2000',
            'author_name' => 'required|string|max:255',
            'author_role' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? LandingTestimonial::max('sort_order') + 1;

        LandingTestimonial::create($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Testimonial created successfully.');
    }

    public function editTestimonial(LandingTestimonial $testimonial): View
    {
        return view('admin.landing.edit_testimonial', compact('testimonial'));
    }

    public function updateTestimonial(Request $request, LandingTestimonial $testimonial): RedirectResponse
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'text' => 'required|string|max:2000',
            'author_name' => 'required|string|max:255',
            'author_role' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $testimonial->update($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    public function destroyTestimonial(LandingTestimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()->route('admin.landing.index')
            ->with('success', 'Testimonial deleted successfully.');
    }

    // ─── FAQs ────────────────────────────────────────────────

    public function storeFaq(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? LandingFaq::max('sort_order') + 1;

        LandingFaq::create($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'FAQ created successfully.');
    }

    public function editFaq(LandingFaq $faq): View
    {
        return view('admin.landing.edit_faq', compact('faq'));
    }

    public function updateFaq(Request $request, LandingFaq $faq): RedirectResponse
    {
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $faq->update($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroyFaq(LandingFaq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.landing.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    // ─── Stats ───────────────────────────────────────────────

    public function storeStat(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? LandingStat::max('sort_order') + 1;

        LandingStat::create($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Stat created successfully.');
    }

    public function editStat(LandingStat $stat): View
    {
        return view('admin.landing.edit_stat', compact('stat'));
    }

    public function updateStat(Request $request, LandingStat $stat): RedirectResponse
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $stat->update($data);

        return redirect()->route('admin.landing.index')
            ->with('success', 'Stat updated successfully.');
    }

    public function destroyStat(LandingStat $stat): RedirectResponse
    {
        $stat->delete();

        return redirect()->route('admin.landing.index')
            ->with('success', 'Stat deleted successfully.');
    }
}
