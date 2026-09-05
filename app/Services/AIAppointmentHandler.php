<?php

namespace App\Services;

use App\Models\AIConversation;
use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\User;
use Illuminate\Support\Str;

class AIAppointmentHandler
{
    /**
     * Initiate AI conversation for appointment request.
     */
    public function initiateConversation(AppointmentRequest $request, string $conversationType = 'voice'): AIConversation
    {
        $conversation = AIConversation::create([
            'tenant_id' => $request->tenant_id,
            'appointment_request_id' => $request->id,
            'customer_phone' => $request->customer_phone,
            'conversation_type' => $conversationType,
            'status' => 'initiated',
        ]);

        return $conversation;
    }

    /**
     * Process user input (digit pressed or voice recognized).
     */
    public function processUserInput(AIConversation $conversation, $input): array
    {
        $response = match ((int) $input) {
            1 => $this->handleBooking($conversation),
            2 => $this->handleCancellation($conversation),
            3 => $this->handlePostponement($conversation),
            default => ['prompt' => 'I didn\'t understand that. Please press 1 to book, 2 to cancel, or 3 to postpone.'],
        };

        return $response;
    }

    /**
     * Handle booking appointment based on AI conversation.
     */
    private function handleBooking(AIConversation $conversation): array
    {
        $appointmentRequest = $conversation->appointmentRequest;
        $tenant = $conversation->tenant;

        // Get first available staff member
        $staff = $tenant->staffUsers()->first();

        if (! $staff) {
            return ['prompt' => 'Sorry, no staff members are available right now. Please try again later.'];
        }

        // Create or get customer
        $customer = User::where('tenant_id', $tenant->id)
            ->where('email', $appointmentRequest->customer_email)
            ->first();

        if (! $customer) {
            $customer = User::create([
                'tenant_id' => $tenant->id,
                'name' => $appointmentRequest->customer_name,
                'email' => $appointmentRequest->customer_email,
                'phone' => $appointmentRequest->customer_phone,
                'password' => bcrypt(Str::random(16)),
                'role' => 'customer',
            ]);
        }

        // Create appointment
        $appointment = Appointment::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'staff_id' => $staff->id,
            'service' => $appointmentRequest->service,
            'scheduled_at' => $appointmentRequest->preferred_at ?? now()->addHour(),
            'status' => 'confirmed',
            'notes' => 'Booked via AI conversation',
        ]);

        // Update conversation and request
        $conversation->markAsCompleted('booked', [
            'appointment_id' => $appointment->id,
        ]);

        $appointmentRequest->markAsScheduled();

        $appointmentDate = $appointment->scheduled_at->format('M d, Y at g:i A');

        return [
            'action' => 'booked',
            'appointment_id' => $appointment->id,
            'prompt' => "Great! Your appointment has been confirmed for {$appointmentDate} for {$appointment->service}. You'll receive a confirmation SMS shortly.",
        ];
    }

    /**
     * Handle cancellation of appointment.
     */
    private function handleCancellation(AIConversation $conversation): array
    {
        $appointmentRequest = $conversation->appointmentRequest;
        $tenant = $conversation->tenant;

        // Find upcoming appointments for this customer
        $appointment = Appointment::where('tenant_id', $tenant->id)
            ->whereHas('customer', function ($q) use ($appointmentRequest) {
                $q->where('phone', $appointmentRequest->customer_phone);
            })
            ->where('scheduled_at', '>', now())
            ->where('status', '!=', 'cancelled')
            ->first();

        if (! $appointment) {
            return ['prompt' => 'You don\'t have any upcoming appointments to cancel. Is there anything else I can help you with?'];
        }

        // Cancel the appointment
        $appointment->update(['status' => 'cancelled']);

        $conversation->markAsCompleted('cancelled', [
            'appointment_id' => $appointment->id,
        ]);

        $appointmentDate = $appointment->scheduled_at->format('M d, Y at g:i A');

        return [
            'action' => 'cancelled',
            'appointment_id' => $appointment->id,
            'prompt' => "Your appointment on {$appointmentDate} has been cancelled. We're sorry to see you go! Feel free to reach out if you'd like to reschedule.",
        ];
    }

    /**
     * Handle postponement of appointment.
     */
    private function handlePostponement(AIConversation $conversation): array
    {
        $appointmentRequest = $conversation->appointmentRequest;
        $tenant = $conversation->tenant;

        // Find upcoming appointments for this customer
        $appointment = Appointment::where('tenant_id', $tenant->id)
            ->whereHas('customer', function ($q) use ($appointmentRequest) {
                $q->where('phone', $appointmentRequest->customer_phone);
            })
            ->where('scheduled_at', '>', now())
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'postponed')
            ->first();

        if (! $appointment) {
            return ['prompt' => 'You don\'t have any upcoming appointments to postpone. Would you like to book a new one?'];
        }

        // For now, we'll postpone by 1 week (in production, the AI could ask for preferred date)
        $newDate = $appointment->scheduled_at->addWeek();

        $appointment->update([
            'status' => 'postponed',
            'scheduled_at' => $newDate,
            'metadata' => array_merge($appointment->metadata ?? [], [
                'original_date' => $appointment->scheduled_at->toIso8601String(),
                'postponed_date' => $newDate->toIso8601String(),
                'postponement_count' => ($appointment->metadata['postponement_count'] ?? 0) + 1,
            ]),
        ]);

        $conversation->markAsCompleted('postponed', [
            'appointment_id' => $appointment->id,
        ]);

        $oldDate = $appointment->scheduled_at->format('M d, Y at g:i A');
        $newDateFormatted = $newDate->format('M d, Y at g:i A');

        return [
            'action' => 'postponed',
            'appointment_id' => $appointment->id,
            'prompt' => "Your appointment has been postponed from {$oldDate} to {$newDateFormatted}. You'll receive a confirmation message shortly.",
        ];
    }

    /**
     * Log conversation transcript.
     */
    public function logTranscript(AIConversation $conversation, string $transcript): void
    {
        $conversation->update([
            'conversation_transcript' => $transcript,
        ]);
    }

    /**
     * Mark conversation as failed.
     */
    public function markAsFailed(AIConversation $conversation, string $reason): void
    {
        $conversation->markAsFailed($reason);
    }
}
