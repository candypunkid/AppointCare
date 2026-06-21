<?php

namespace Modules\Twilio\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Jobs\InitiateAICall;
use Modules\Twilio\Services\TwilioService;

class TwilioWebhookController extends Controller
{
    public function __construct(
        private TwilioService $twilioService
    ) {}

    /**
     * Handle incoming Twilio webhook for stream setup
     */
    public function handleStream(Request $request): Response
    {
        $appointmentId = $request->query('appointment_id');
        $tenantSlug = $request->header('X-Tenant-Slug') ?? 'default';

        $twiML = $this->twilioService->generateStreamTwiML($appointmentId, $tenantSlug);

        return response($twiML, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Handle Twilio status callback
     */
    public function handleStatus(Request $request): Response
    {
        $callSid = $request->input('CallSid');
        $callStatus = $request->input('CallStatus');
        $appointmentId = $request->input('appointment_id');

        if ($appointmentId && $appointment = Appointment::find($appointmentId)) {
            $this->twilioService->handleStatusCallback($callSid, $callStatus, $appointment);
        }

        return response('', 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Health check endpoint
     */
    public function health(): Response
    {
        return response()->json(['status' => 'ok']);
    }
}
