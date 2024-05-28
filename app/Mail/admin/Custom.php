<?php

namespace App\Mail\admin;

use App\Models\Configuration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Custom extends Mailable
{
    use Queueable, SerializesModels;

    public $custom_subject, $custom_message, $attachmentPaths;

    public function __construct(
        $custom_subject,
        $custom_message,
        $attachmentPaths
    ) {
        $this->custom_subject = $custom_subject;
        $this->custom_message = $custom_message;
        $this->attachmentPaths = $attachmentPaths;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->custom_subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.custom',
            with: [
                'logo' => Configuration::first()->logo,
                'custom_message' => $this->custom_message,
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
        $attachments = [];
        // Check if attachment paths are available and not empty
        if (!empty($this->attachmentPaths)) {
            // Loop through each attachment path
             foreach ($this->attachmentPaths as $attachmentPath) {
                // Check if the attachment path exists
                if (Storage::exists($attachmentPath)) {
                    // Create an attachment instance and add it to the attachments array
                    $attachments[] = Attachment::fromStorage($attachmentPath);
                } else {
                    Log::warning("Attachment not found at path: $attachmentPath");
                }
            }
        }
        return $attachments;
    }

}
