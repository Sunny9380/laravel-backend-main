<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'vendor_id',
        'gst_number',
        'phone_number',
        'email',
        'is_active',
        'razorpay_id'
    ];

    public function more_info(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function properties()
    {
        return $this->hasMany(Hotel::class, 'vendor_id', 'id');
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class, 'vendor_id', 'id');
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Hotel::class, 'vendor_id', 'hotel_id', 'id', 'id');
    }
}
