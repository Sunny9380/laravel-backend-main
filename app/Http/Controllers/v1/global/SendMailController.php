<?php

namespace App\Http\Controllers\v1\global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendMailController extends Controller
{
    public function sendMail()
    {
        Mail::send('mails/welcome', [], function ($message) {
            $message->to('vermamanav110@gmail.com')->subject('Mailjet Testing');
        });
        dd('Mail Send Successfully');
    }
}
