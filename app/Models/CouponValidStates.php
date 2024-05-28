<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponValidStates extends Model
{
    use HasFactory;

    protected $table = 'coupons_valid_states';

    protected $fillable = [
        'coupon_id',
        'state_id'
    ];
}
