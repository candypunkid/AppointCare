<?php

namespace App\Services;

use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingHowStep;
use App\Models\LandingIndustry;
use App\Models\LandingPlan;
use App\Models\LandingStat;
use App\Models\LandingTestimonial;
use App\Models\Tenant;

class LandingPageService
{
    public function getLandingData(?Tenant $tenant): array
    {
        $tenantId = $tenant?->id;

        return [
            'features' => $this->getFeatures($tenantId),
            'howSteps' => $this->getHowSteps($tenantId),
            'industries' => $this->getIndustries($tenantId),
            'plans' => $this->getPlans($tenantId),
            'testimonials' => $this->getTestimonials($tenantId),
            'faqs' => $this->getFaqs($tenantId),
            'stats' => $this->getStats($tenantId),
        ];
    }

    protected function getFeatures(?int $tenantId)
    {
        return LandingFeature::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id');
            if ($tenantId) {
                $q->orWhere('tenant_id', $tenantId);
            }
        })->orderBy('sort_order')->get();
    }

    protected function getHowSteps(?int $tenantId)
    {
        return LandingHowStep::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id');
            if ($tenantId) {
                $q->orWhere('tenant_id', $tenantId);
            }
        })->orderBy('sort_order')->get();
    }

    protected function getIndustries(?int $tenantId)
    {
        return LandingIndustry::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id');
            if ($tenantId) {
                $q->orWhere('tenant_id', $tenantId);
            }
        })->orderBy('sort_order')->get();
    }

    protected function getPlans(?int $tenantId)
    {
        return LandingPlan::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id');
            if ($tenantId) {
                $q->orWhere('tenant_id', $tenantId);
            }
        })->orderBy('sort_order')->get();
    }

    protected function getTestimonials(?int $tenantId)
    {
        return LandingTestimonial::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id');
            if ($tenantId) {
                $q->orWhere('tenant_id', $tenantId);
            }
        })->orderBy('sort_order')->get();
    }

    protected function getFaqs(?int $tenantId)
    {
        return LandingFaq::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id');
            if ($tenantId) {
                $q->orWhere('tenant_id', $tenantId);
            }
        })->orderBy('sort_order')->get();
    }

    protected function getStats(?int $tenantId)
    {
        return LandingStat::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id');
            if ($tenantId) {
                $q->orWhere('tenant_id', $tenantId);
            }
        })->orderBy('sort_order')->get();
    }
}
