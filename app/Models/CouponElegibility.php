<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponElegibility extends Model
{
    use HasFactory;

    protected $table = 'coupons_eligibility';

    protected $fillable = [
        'coupon_id',
        'price_range_from',
        'price_range_to',
        'user_type',
        'is_new_user_eligible',
        'is_all_users_eligible',
        'is_first_booking',
        'is_price_valid'
    ];

}
