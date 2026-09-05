<?php

namespace Modules\Notification\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Appointment\Models\Appointment;

class AppointmentStatusNotification extends Notification
{
    public function __construct(
        public Appointment $appointment,
        public string $statusMessage = ''
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'sms'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = match ($this->appointment->status) {
            'confirmed' => 'Your appointment has been confirmed!',
            'completed' => 'Your appointment has been completed. Thank you!',
            'failed' => 'We encountered an issue with your appointment call. Please contact support.',
            'cancelled' => 'Your appointment has been cancelled.',
            default => 'Your appointment status has been updated.',
        };

        return (new MailMessage)
            ->subject('Appointment '.ucfirst($this->appointment->status))
            ->greeting("Hello {$this->appointment->customer_name},")
            ->line($message)
            ->line('Appointment Date: '.$this->appointment->appointment_date->format('F j, Y \a\t g:i A'))
            ->when($this->appointment->ai_summary, function (MailMessage $msg) {
                return $msg->line('Summary: '.$this->appointment->ai_summary);
            })
            ->action('View Appointment', url("/appointments/{$this->appointment->id}"))
            ->line('Thank you for using AppointCare!');
    }

    public function toSMS(object $notifiable): array
    {
        $status = ucfirst($this->appointment->status);
        $date = $this->appointment->appointment_date->format('M j, g:i A');

        return [
            'body' => "AppointCare: Your appointment on {$date} is now {$status}. Reply HELP for support.",
            'phone' => $this->appointment->customer_phone,
        ];
    }
}
