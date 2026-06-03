<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AIConversation;
use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Services\AIAppointmentHandler;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    protected AIAppointmentHandler $aiHandler;
    protected TwilioService $twilio;

    public function __construct(AIAppointmentHandler $aiHandler, TwilioService $twilio)
    {
        $this->aiHandler = $aiHandler;
        $this->twilio = $twilio;
    }

    /**
     * Show public appointment booking form.
     */
    public function showBookingForm()
    {
        $tenant = tenant();

        return view('user::appointments.book', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * Submit appointment booking form and create request.
     */
    public function submitBookingForm(Request $request)
    {
        $tenant = tenant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|regex:/^\+?1?\d{9,15}$/',
            'service' => 'required|string|max:255',
            'preferred_date' => 'nullable|date|after:today',
            'preferred_time' => 'nullable|date_format:H:i',
            'message' => 'nullable|string|max:1000',
        ]);

        // Resolve tenant: prefer current tenant, fall back to first tenant if available
        if (! $tenant) {
            $tenant = \App\Models\Tenant::first();
        }

        if (! $tenant) {
            return redirect()->route('appointments.book')
                ->with('warning', 'No tenant is available to receive this booking. Please visit the clinic booking page.');
        }

        // Create appointment request
        $appointmentRequest = AppointmentRequest::create([
            'tenant_id' => $tenant->id,
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],
            'service' => $validated['service'],
            'preferred_at' => $validated['preferred_date'] && $validated['preferred_time']
                ? \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validated['preferred_date'] . ' ' . $validated['preferred_time'])
                : null,
            'message' => $validated['message'] ?? null,
            'status' => 'new',
        ]);

        // Initiate AI call
        $callbackUrl = route('twilio.incoming-call', ['tenant' => $tenant->slug]);
        $result = $this->twilio->initiateVoiceCall(
            $validated['phone'],
            $callbackUrl
        );

        if ($result['success']) {
            // Create AI conversation record
            $conversation = AIConversation::create([
                'tenant_id' => $tenant->id,
                'appointment_request_id' => $appointmentRequest->id,
                'customer_phone' => $validated['phone'],
                'conversation_type' => 'voice',
                'twilio_call_sid' => $result['call_sid'],
                'status' => 'initiated',
            ]);

            $appointmentRequest->markAsContacted();

            return redirect()->route('appointments.book')
                ->with('success', 'We\'re calling you now to confirm your appointment!');
        }

        // If call fails, schedule for manual contact
        return redirect()->route('appointments.book')
            ->with('warning', 'We\'ll contact you shortly to confirm your appointment.');
    }

    /**
     * Show all appointments (protected).
     */
    public function index()
    {
        $this->middleware('auth');
        $tenant = tenant();
        $user = auth()->user();

        if ($user->isAdmin() || $user->isStaff()) {
            $appointments = Appointment::forTenant($tenant)->paginate(15);
        } else {
            $appointments = $user->customerAppointments()->paginate(15);
        }

        return view('user::appointments.index', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Show appointment details.
     */
    public function show(Appointment $appointment)
    {
        $this->middleware('auth');
        $tenant = tenant();
        $user = auth()->user();

        if ($appointment->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($user->isCustomer() && $appointment->customer_id !== $user->id) {
            abort(403);
        }

        return view('user::appointments.show', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * Show edit form (protected).
     */
    public function edit(Appointment $appointment)
    {
        $this->middleware('auth');
        $tenant = tenant();
        $user = auth()->user();

        if ($appointment->tenant_id !== $tenant->id || !$user->isStaff()) {
            abort(403);
        }

        return view('user::appointments.edit', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * Update appointment (protected).
     */
    public function update(Request $request, Appointment $appointment)
    {
        $this->middleware('auth');
        $tenant = tenant();
        $user = auth()->user();

        if ($appointment->tenant_id !== $tenant->id || !$user->isStaff()) {
            abort(403);
        }

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'service' => 'required|string|max:255',
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,postponed',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($validated);

        return redirect()->route('appointment.show', $appointment)
            ->with('success', 'Appointment updated successfully!');
    }

    /**
     * Cancel appointment.
     */
    public function destroy(Appointment $appointment)
    {
        $this->middleware('auth');
        $tenant = tenant();
        $user = auth()->user();

        if ($appointment->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($user->isCustomer() && $appointment->customer_id !== $user->id) {
            abort(403);
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('appointment.index')
            ->with('success', 'Appointment cancelled successfully!');
    }
}
