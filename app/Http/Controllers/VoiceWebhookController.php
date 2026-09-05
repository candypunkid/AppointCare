<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CallLog;
use App\Repositories\CallLogRepository;
use App\Services\ConversationService;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class VoiceWebhookController extends Controller
{
    public function __construct(
        protected TwilioService $twilioService,
        protected ConversationService $conversationService,
        protected CallLogRepository $callLogRepository
    ) {}

    public function handleVoice(Request $request): Response
    {
        $appointmentId = $request->query('appointment_id');
        $speechResult = $request->input('SpeechResult');
        $callSid = $request->input('CallSid');

        Log::info('Voice webhook received', [
            'appointment_id' => $appointmentId,
            'call_sid' => $callSid,
            'speech' => $speechResult,
        ]);

        $appointment = null;
        if ($appointmentId) {
            $appointment = Appointment::with('customer')->find($appointmentId);
        }

        if (! $appointment) {
            $twiml = $this->twilioService->generateSimpleResponseTwiML('Sorry, we could not find your appointment details. Goodbye.');
            $twiml->hangup();

            return response($twiml->asXML(), 200)->header('Content-Type', 'application/xml');
        }

        $callLog = $callSid ? $this->callLogRepository->findByTwilioSid($callSid) : null;

        if (! $callLog) {
            $callLog = $this->conversationService->startCallLog($appointment->id, $callSid ?: 'unknown');
        }

        if ($speechResult) {
            $this->conversationService->appendMessage($callLog, 'customer', $speechResult);
        }

        if (! $speechResult) {
            $appointmentData = [
                'customer_name' => $appointment->customer?->name ?? 'there',
                'service' => $appointment->service ?? 'appointment',
                'time' => $appointment->scheduled_at?->format('g:i A') ?? '',
                'date' => $appointment->scheduled_at?->format('l, F jS') ?? '',
            ];

            $twiml = $this->twilioService->generateAppointmentReminderTwiML(
                $appointmentData,
                $this->getVoiceEndpointUrl($appointmentId)
            );

            return response($twiml->asXML(), 200)->header('Content-Type', 'application/xml');
        }

        $appointmentData = [
            'service' => $appointment->service ?? 'Appointment',
            'date' => $appointment->scheduled_at?->format('Y-m-d') ?? '',
            'time' => $appointment->scheduled_at?->format('g:i A') ?? '',
        ];

        $result = $this->conversationService->processConversation($callLog, $appointmentData);

        return $this->buildTwiMLResponse($result, $appointment, $callLog, $appointmentId);
    }

    public function handleStatus(Request $request): Response
    {
        $callSid = $request->input('CallSid');
        $callStatus = $request->input('CallStatus');
        $appointmentId = $request->query('appointment_id');
        $duration = $request->input('CallDuration');

        Log::info('Call status update received', [
            'call_sid' => $callSid,
            'status' => $callStatus,
            'appointment_id' => $appointmentId,
            'duration' => $duration,
        ]);

        if ($callSid) {
$callLog = $callSid ? $this->callLogRepository->findByTwilioSid($callSid) : null;

            if ($callLog) {
                $updateData = [];

                match ($callStatus) {
                    'completed' => $updateData['status'] = 'completed',
                    'failed', 'busy', 'no-answer' => $updateData['status'] = 'failed',
                    'answered' => $updateData['status'] = 'in_progress',
                    default => null,
                };

                if ($duration) {
                    $updateData['duration'] = (int) $duration;
                }

                if (! empty($updateData)) {
                    $callLog->update($updateData);
                }
            }
        }

        if ($appointmentId && in_array($callStatus, ['failed', 'busy', 'no-answer', 'completed'])) {
            $appointment = Appointment::find($appointmentId);
            if ($appointment && ! in_array($appointment->status, ['confirmed', 'cancelled', 'rescheduled'])) {
                $appointment->update([
                    'status' => $callStatus === 'completed' ? 'pending' : 'failed',
                ]);
            }
        }

        return response('', 200)->header('Content-Type', 'application/xml');
    }

    public function handleOutboundCall(Request $request): Response
    {
        $appointmentId = $request->query('appointment_id');

        $appointment = null;
        if ($appointmentId) {
            $appointment = Appointment::with('customer')->find($appointmentId);
        }

        if (! $appointment) {
            $twiml = $this->twilioService->generateSimpleResponseTwiML('Thank you for calling AppointCare. Goodbye.');
            $twiml->hangup();

            return response($twiml->asXML(), 200)->header('Content-Type', 'application/xml');
        }

        $callSid = $request->input('CallSid');
        if ($callSid) {
            $this->conversationService->startCallLog($appointment->id, $callSid);
        }

        $appointmentData = [
            'customer_name' => $appointment->customer?->name ?? 'there',
            'service' => $appointment->service ?? 'appointment',
            'time' => $appointment->scheduled_at?->format('g:i A') ?? '',
            'date' => $appointment->scheduled_at?->format('l, F jS') ?? '',
        ];

        $twiml = $this->twilioService->generateAppointmentReminderTwiML(
            $appointmentData,
            $this->getVoiceEndpointUrl($appointmentId)
        );

        return response($twiml->asXML(), 200)->header('Content-Type', 'application/xml');
    }

    public function handleIncomingCall(Request $request): Response
    {
        $twiml = $this->twilioService->generateSimpleResponseTwiML(
            'Thank you for calling AppointCare. Our office hours are Monday to Friday, 9 AM to 5 PM. Please call back during business hours, or visit our website to book online.'
        );
        $twiml->hangup();

        return response($twiml->asXML(), 200)->header('Content-Type', 'application/xml');
    }

    public function handleGather(Request $request): Response
    {
        $speechResult = $request->input('SpeechResult');
        $digits = $request->input('Digits');
        $appointmentId = $request->query('appointment_id');

        if ($digits === '0') {
            $twiml = $this->twilioService->generateSimpleResponseTwiML('Transferring you to a representative.');
            $adminPhone = config('appointcare.admin_phone', '');
            if ($adminPhone) {
                $twiml->dial($adminPhone);
            } else {
                $twiml->say('No representative is available at this time. Please call again later.');
                $twiml->hangup();
            }

            return response($twiml->asXML(), 200)->header('Content-Type', 'application/xml');
        }

        return $this->handleVoice($request);
    }

    protected function buildTwiMLResponse(array $result, Appointment $appointment, CallLog $callLog, ?string $appointmentId): Response
    {
        $intent = $result['intent'];
        $message = $result['response_message'];

        if ($intent === 'transfer_to_human') {
            $twiml = $this->twilioService->generateSimpleResponseTwiML($message);
            $adminPhone = config('appointcare.admin_phone', '');
            if ($adminPhone) {
                $twiml->dial($adminPhone);
            } else {
                $twiml->say('Our team is currently unavailable. We will call you back shortly.');
                $twiml->hangup();
            }

            return response($twiml->asXML(), 200)->header('Content-Type', 'application/xml');
        }

        if (in_array($intent, ['confirm_appointment', 'cancel_appointment'])) {
            $twiml = $this->twilioService->generateSimpleResponseTwiML($message);
            $twiml->hangup();

            return response($twiml->asXML(), 200)->header('Content-Type', 'application/xml');
        }

        if ($intent === 'reschedule_appointment' && (($result['new_date'] ?? '') !== '' || ($result['new_time'] ?? '') !== '')) {
            $twiml = $this->twilioService->generateSimpleResponseTwiML($message);
            $twiml->hangup();

            return response($twiml->asXML(), 200)->header('Content-Type', 'application/xml');
        }

        $twiml = $this->twilioService->generateSimpleResponseTwiML($message);

        $twiml->gather([
            'input' => 'speech',
            'action' => $this->getVoiceEndpointUrl($appointmentId),
            'method' => 'POST',
            'timeout' => 10,
            'language' => 'en-US',
            'speechTimeout' => 'auto',
        ]);

        return response($twiml->asXML(), 200)->header('Content-Type', 'application/xml');
    }

    protected function getVoiceEndpointUrl(?string $appointmentId): string
    {
        $url = route('api.twilio.voice');
        if ($appointmentId) {
            $url .= '?appointment_id='.$appointmentId;
        }

        return $url;
    }
}
