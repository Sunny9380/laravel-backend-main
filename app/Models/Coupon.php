<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $table = 'coupons';

    protected $fillable = [
        'name',
        'code',
        'description',
        'limit_per_use',
        'is_discount_in_percent',
        'discount',
        'valid_from',
        'valid_till',
        'no_end_date',
        'num_of_coupons',
        'conditions',
        'is_active'
    ];

    public function couponElegibility()
    {
        return $this->hasOne(CouponElegibility::class, 'coupon_id');
    }

    public function couponValidStates()
    {
        return $this->hasMany(CouponValidStates::class, 'coupon_id');
    }

    public function couponValidProperties()
    {
        return $this->hasMany(CouponValidProperties::class, 'coupon_id');
    }

}
