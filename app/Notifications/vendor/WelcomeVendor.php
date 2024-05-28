<?php

namespace App\Notifications\vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeVendor extends Notification implements ShouldQueue
{
    use Queueable;

    private $vendorData;

    /**
     * Create a new notification instance.
     */
    public function __construct($vendorData)
    {
        $this->vendorData = $vendorData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line($this->vendorData['body'])
                    ->action($this->vendorData['actionText'],
                    url($this->vendorData['actionUrl']))
                    ->line($this->vendorData['thanks']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
