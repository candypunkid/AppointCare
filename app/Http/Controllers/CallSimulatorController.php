<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CallSimulatorController extends Controller
{
    /**
     * Render the local call simulator page.
     */
    public function index(): View
    {
        $appointments = Appointment::with('customer')
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->latest()
            ->take(30)
            ->get()
            ->map(fn (Appointment $a) => [
                'id' => $a->id,
                'customer_name' => $a->customer?->name ?? 'Unknown',
                'phone' => $a->customer?->phone ?? '',
                'service' => $a->service ?? 'Appointment',
                'scheduled_at' => $a->scheduled_at?->format('M d, Y g:i A') ?? '',
                'status' => $a->status,
            ]);

        return view('simulator', compact('appointments'));
    }

    /**
     * Return the current status of an appointment (used by the simulator UI).
     */
    public function appointmentStatus(int $appointmentId): JsonResponse
    {
        $appointment = Appointment::find($appointmentId);

        if (! $appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $appointment->id,
                'status' => $appointment->status,
                'scheduled_at' => $appointment->scheduled_at?->toDateTimeString(),
            ],
        ]);
    }
}
