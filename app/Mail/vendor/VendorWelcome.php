<?php

namespace App\Mail\vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $vendor_id, $email, $password;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->vendor_id = $data['vendor_id'];
        $this->email = $data['email'];
        $this->password = $data['password'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . env('APP_NAME'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor.welcome',
            with: [
                'vendor_id' => $this->vendor_id,
                'email' => $this->email,
                'password' => $this->password
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
