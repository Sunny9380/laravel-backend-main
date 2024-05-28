<?php

namespace App\Jobs\admin;

use App\Mail\admin\Custom;
use App\Mail\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendBulkMails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    private $emails, $subject, $message, $attachmentPaths;

    public function __construct(
        $emails,
        $subject,
        $message,
        $attachmentPaths=null
    ) {
        $this->emails = $emails;
        $this->subject = $subject;
        $this->message = $message;
        $this->attachmentPaths = $attachmentPaths;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->emails as $email) {
            Mail::to($email)
                ->send(
                    new Custom(
                        $this->subject,
                        $this->message,
                        $this->attachmentPaths
                    )
                );
        }
        foreach ($this->attachmentPaths as $path) {
              Storage::delete($path);
        }
    }
}
