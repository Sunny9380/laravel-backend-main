<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestVerifiedMail extends Model
{
    use HasFactory;

    protected $table = 'request_verified_mail';

    protected $fillable = [
        'user_id',
        'email',
        'times_sent',
        'verified_at',
        'mail_sent_at',
        'verify_token',
    ];
}
