<?php

namespace App\Http\Controllers;

use App\Jobs\MakeReminderCallJob;
use App\Models\Appointment;
use App\Models\Tenant;
use App\Models\User;
use App\Repositories\AppointmentRepository;
use App\Services\AppointmentService;
use App\Services\ConversationService;
use App\Services\OpenAIService;
use App\Services\TwilioService;
use App\Support\PhoneHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AppointmentAIController extends Controller
{
    public function __construct(
        protected OpenAIService $openAIService,
        protected ConversationService $conversationService,
        protected AppointmentService $appointmentService,
        protected TwilioService $twilioService,
        protected AppointmentRepository $appointmentRepository
    ) {}

    public function analyzeIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conversation' => 'required|array|min:1',
            'conversation.*.speaker' => 'required|string|in:ai,customer',
            'conversation.*.message' => 'required|string|max:2000',
            'appointment_id' => 'nullable|integer|exists:appointments,id',
        ]);

        $appointmentData = [];
        if (! empty($validated['appointment_id'])) {
            $appointment = $this->appointmentRepository->findById($validated['appointment_id']);
            if ($appointment) {
                $appointmentData = [
                    'service' => $appointment->service ?? 'Appointment',
                    'date' => $appointment->scheduled_at?->format('Y-m-d') ?? '',
                    'time' => $appointment->scheduled_at?->format('g:i A') ?? '',
                ];
            }
        }

        $result = $this->openAIService->analyzeIntent($validated['conversation'], $appointmentData);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'time' => 'nullable|date_format:H:i',
        ]);

        $availability = $this->appointmentService->checkSlotAvailability(
            $validated['date'],
            $validated['time'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => $availability,
        ]);
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'service' => 'nullable|string|max:255',
        ]);

        $slots = $this->appointmentService->findAvailableSlots(
            $validated['date'],
            $validated['service'] ?? ''
        );

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $validated['date'],
                'available_slots' => $slots,
            ],
        ]);
    }

    public function triggerReminderCall(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'appointment_id' => 'required|integer|exists:appointments,id',
        ]);

        $appointment = $this->appointmentRepository->findById($validated['appointment_id']);

        if (! $appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found.'], 404);
        }

        if (! $appointment->customer?->phone) {
            return response()->json(['success' => false, 'message' => 'Customer has no phone number.'], 422);
        }

        if (! in_array($appointment->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment is not in a callable status.',
                'status' => $appointment->status,
            ], 422);
        }

        MakeReminderCallJob::dispatch($appointment);

        return response()->json([
            'success' => true,
            'message' => 'Reminder call has been queued.',
        ]);
    }

    public function conversationHistory(Request $request, int $appointmentId): JsonResponse
    {
        $appointment = $this->appointmentRepository->findWithCallLogs($appointmentId);

        if (! $appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found.'], 404);
        }

        $history = $appointment->callLogs->map(function ($callLog) {
            return [
                'call_log_id' => $callLog->id,
                'call_sid' => $callLog->twilio_call_sid,
                'intent' => $callLog->detected_intent,
                'duration' => $callLog->duration,
                'status' => $callLog->status,
                'conversation' => $callLog->conversationLogs->sortBy('created_at')->values()->map(fn ($log) => [
                    'speaker' => $log->speaker,
                    'message' => $log->message,
                    'time' => $log->created_at?->toIso8601String(),
                ]),
                'created_at' => $callLog->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    public function dashboardAnalytics(): JsonResponse
    {
        $analytics = $this->appointmentRepository->getAnalytics();

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    public function bookAndCall(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date|after:now',
            'service' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $tenantId = tenant_id() ?? Tenant::where('is_active', true)->value('id');
            $customerPhone = PhoneHelper::normalizeToE164($validated['customer_phone']);

            $customer = User::where('email', $validated['customer_email'])->first();

            if (! $customer) {
                $customer = User::create([
                    'tenant_id' => $tenantId,
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                    'phone' => $customerPhone,
                    'password' => bcrypt(Str::random(16)),
                    'role' => 'customer',
                ]);
            } else {
                $customer->update(['phone' => $customerPhone]);
            }

            $appointment = Appointment::create([
                'tenant_id' => $tenantId,
                'customer_id' => $customer->id,
                'service' => $validated['service'] ?? 'General Appointment',
                'scheduled_at' => $validated['appointment_date'],
                'status' => 'pending',
                'notes' => $validated['description'] ?? null,
            ]);

            MakeReminderCallJob::dispatch($appointment);

            return response()->json([
                'success' => true,
                'message' => 'Appointment created. We are calling you now to confirm.',
                'appointment_id' => $appointment->id,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Booking + call failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment. '.$e->getMessage(),
            ], 500);
        }
    }
}
