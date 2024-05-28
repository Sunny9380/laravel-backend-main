<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponValidProperties extends Model
{
    use HasFactory;

    protected $table = 'coupon_valid_properties';

    protected $fillable = [
        'coupon_id',
        'property_id',
    ];
}
