<?php

namespace App\Http\Controllers;

use App\Jobs\MakeReminderCallJob;
use App\Repositories\AppointmentRepository;
use App\Repositories\CallLogRepository;
use App\Services\ConversationService;
use App\Services\OpenAIService;
use App\Services\TwilioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TwilioController extends Controller
{
    public function __construct(
        protected TwilioService $twilioService,
        protected ConversationService $conversationService,
        protected OpenAIService $openAIService,
        protected AppointmentRepository $appointmentRepository,
        protected CallLogRepository $callLogRepository
    ) {}

    public function initiateOutboundCall(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'appointment_id' => 'required|integer|exists:appointments,id',
        ]);

        $appointment = $this->appointmentRepository->findById($validated['appointment_id']);

        if (! $appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found.'], 404);
        }

        MakeReminderCallJob::dispatch($appointment);

        return response()->json([
            'success' => true,
            'message' => 'Reminder call queued successfully.',
            'appointment_id' => $appointment->id,
        ]);
    }

    public function sendSMSNotification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'appointment_id' => 'required|integer|exists:appointments,id',
            'message' => 'required|string|max:1600',
        ]);

        $appointment = $this->appointmentRepository->findById($validated['appointment_id']);

        if (! $appointment || ! $appointment->customer?->phone) {
            return response()->json(['success' => false, 'message' => 'Customer phone not found.'], 404);
        }

        $result = $this->twilioService->sendSMS(
            $appointment->customer->phone,
            $validated['message']
        );

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => $result['error']], 500);
        }

        return response()->json([
            'success' => true,
            'message_sid' => $result['message_sid'],
        ]);
    }

    public function callLogs(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $logs = $this->callLogRepository->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function analytics(): JsonResponse
    {
        $analytics = $this->appointmentRepository->getAnalytics();

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }
}
