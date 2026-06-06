<?php

namespace Modules\Appointment\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Appointment\Http\Requests\StoreAppointmentRequest;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Jobs\InitiateAICall;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        try {
            $appointment = Appointment::create($request->validated());
            InitiateAICall::dispatch($appointment);

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully. We will call you shortly.',
                'appointment_id' => $appointment->id,
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Appointment creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment. Please try again.',
            ], 500);
        }
    }

    public function show(Appointment $appointment): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $appointment,
        ]);
    }

    public function retryCall(Appointment $appointment): JsonResponse
    {
        try {
            $appointment->update(['status' => 'pending']);
            InitiateAICall::dispatch($appointment);

            return response()->json([
                'success' => true,
                'message' => 'Retry call initiated.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retry call.',
            ], 500);
        }
    }

    public function index(): JsonResponse
    {
        $appointments = Appointment::latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $appointments,
        ]);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,completed,failed,cancelled',
            'notes' => 'sometimes|string',
        ]);

        $appointment->update($validated);

        return response()->json([
            'success' => true,
            'data' => $appointment,
        ]);
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted.',
        ]);
    }
}
