<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Mailgun\Mailgun;

class MailgunServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Mailgun::class, function($app){
            return Mailgun::create(env('MAILGUN_SECRET', ''));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
