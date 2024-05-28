<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAccountSettings extends Model
{
    use HasFactory;

    protected $table = 'admin_account_settings';

    protected $fillable = [
        'razorpay_id'
    ];
}
