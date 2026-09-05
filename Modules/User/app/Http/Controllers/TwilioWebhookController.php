<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AIConversation;
use App\Models\AppointmentRequest;
use App\Models\Tenant;
use App\Services\AIAppointmentHandler;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Twilio\Twiml\VoiceResponse;

class TwilioWebhookController extends Controller
{
    protected TwilioService $twilio;

    protected AIAppointmentHandler $aiHandler;

    public function __construct(TwilioService $twilio, AIAppointmentHandler $aiHandler)
    {
        $this->twilio = $twilio;
        $this->aiHandler = $aiHandler;
    }

    /**
     * Handle incoming Twilio call and initiate appointment conversation.
     */
    public function handleIncomingCall(Request $request): Response
    {
        $tenant = $this->getTenantFromRequest($request);
        $callSid = $request->input('CallSid');
        $from = $request->input('From');

        // Get or create conversation
        $conversation = AIConversation::where('twilio_call_sid', $callSid)->first();

        if (! $conversation) {
            $conversation = AIConversation::create([
                'tenant_id' => $tenant->id,
                'customer_phone' => $from,
                'conversation_type' => 'voice',
                'twilio_call_sid' => $callSid,
                'status' => 'initiated',
            ]);
        }

        $conversation->markAsInProgress();

        $gatherUrl = route('twilio.handle-input', ['tenant' => $tenant->slug]);
        $twiml = $this->twilio->generateAppointmentBookingTwiML($tenant->name, $gatherUrl);

        return response($twiml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * Handle user input (digit pressed).
     */
    public function handleUserInput(Request $request): Response
    {
        $tenant = $this->getTenantFromRequest($request);
        $callSid = $request->input('CallSid');
        $digit = $request->input('Digits');

        $conversation = AIConversation::where('twilio_call_sid', $callSid)->first();

        if (! $conversation) {
            return $this->errorResponse('Conversation not found');
        }

        // Process the input
        $result = $this->aiHandler->processUserInput($conversation, $digit);

        $twiml = new VoiceResponse;

        if (isset($result['action'])) {
            // Action was taken (booked, cancelled, postponed)
            $twiml->say($result['prompt'], ['voice' => 'alice']);
            $twiml->hangup();
        } else {
            // No action, ask again
            $twiml->say($result['prompt'], ['voice' => 'alice']);

            $gatherUrl = route('twilio.handle-input', ['tenant' => $tenant->slug]);
            $twiml->gather([
                'numDigits' => 1,
                'action' => $gatherUrl,
                'method' => 'POST',
                'timeout' => 5,
            ]);
        }

        return response($twiml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * Handle call status updates.
     */
    public function handleCallStatus(Request $request): Response
    {
        $callSid = $request->input('CallSid');
        $status = $request->input('CallStatus');
        $recordingUrl = $request->input('RecordingUrl');

        $conversation = AIConversation::where('twilio_call_sid', $callSid)->first();

        if ($conversation) {
            $metadata = $conversation->metadata ?? [];
            $metadata['call_status'] = $status;
            if ($recordingUrl) {
                $metadata['recording_url'] = $recordingUrl;
            }

            if ($status === 'completed') {
                $conversation->update([
                    'status' => 'completed',
                    'ended_at' => now(),
                    'metadata' => $metadata,
                ]);
            }
        }

        return response('OK', 200);
    }

    /**
     * Initiate AI call to customer for appointment request.
     */
    public function initiateAppointmentCall(Request $request)
    {
        $validated = $request->validate([
            'appointment_request_id' => 'required|exists:appointment_requests,id',
        ]);

        $tenant = tenant();
        $appointmentRequest = AppointmentRequest::where('tenant_id', $tenant->id)
            ->findOrFail($validated['appointment_request_id']);

        // Create conversation record
        $conversation = $this->aiHandler->initiateConversation($appointmentRequest, 'voice');

        // Initiate Twilio call
        $callbackUrl = route('twilio.incoming-call', ['tenant' => $tenant->slug]);
        $result = $this->twilio->initiateVoiceCall(
            $appointmentRequest->customer_phone,
            $callbackUrl
        );

        if ($result['success']) {
            $conversation->update(['twilio_call_sid' => $result['call_sid']]);

            return response()->json([
                'success' => true,
                'message' => 'Call initiated successfully',
                'conversation_id' => $conversation->id,
                'call_sid' => $result['call_sid'],
            ]);
        }

        $conversation->markAsFailed($result['error']);

        return response()->json([
            'success' => false,
            'message' => 'Failed to initiate call',
            'error' => $result['error'],
        ], 400);
    }

    /**
     * Get tenant from request (subdomain-based routing).
     */
    protected function getTenantFromRequest(Request $request): Tenant
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        $subdomain = $parts[0];

        return Tenant::where('slug', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * Return error response.
     */
    protected function errorResponse(string $message): Response
    {
        $twiml = new VoiceResponse;
        $twiml->say("Sorry, something went wrong. {$message}", ['voice' => 'alice']);
        $twiml->hangup();

        return response($twiml, 200, ['Content-Type' => 'application/xml']);
    }
}
