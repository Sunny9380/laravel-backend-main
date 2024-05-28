<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $table = 'offers';

    protected $fillable = [
        'heading',
        'description',
        'background_image',
        'is_coupon_code',
        'coupon_code',
        'start_date',
        'end_date',
        'is_active',
    ];
}
