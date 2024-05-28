<?php

namespace App\Jobs\vendor;

use App\Mail\vendor\VendorWelcome;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class WelcomeVendorMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $vendorData;

    /**
     * Create a new job instance.
     */
    public function __construct($vendorData)
    {
        $this->vendorData = $vendorData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->vendorData['email'])
            ->send(
                new VendorWelcome(
                    $this->vendorData
                )
            );
    }
}
