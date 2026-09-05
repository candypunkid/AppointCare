<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Contracts\View\View;

class PublicBookingController extends Controller
{
    public function show(): View
    {
        $services = ['Consultation', 'Checkup', 'Haircut', 'Dental Cleaning', 'Massage Therapy', 'General Appointment'];

        $minDate = now()->addDay()->toDateString();
        $tenant = tenant() ?? Tenant::where('is_active', true)->first();

        return view('public.booking', compact('services', 'minDate', 'tenant'));
    }
}
