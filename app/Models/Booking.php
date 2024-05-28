<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $table = "bookings";

    protected $fillable = [
        'user_id',
        'hotel_id',
        'name',
        'guest_name',
        'amount',
        'coupon_id',
        'email',
        'primary_phone_number',
        'secondary_phone_number',
        'notes',
        'check_in',
        'check_out',
        'check_in_hours',
        'check_in_time',
        'razorpay_payment_id',
        'razorpay_signature',
        'payment_status',
        'payment_method',
        'transfer_id',
        'transfer_status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            $book->booking_id = "STAY" . Str::random(4) . time();
        });
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }

    public function coupon(){
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function bookedRooms()
    {
        return $this->hasMany(BookedRoom::class, 'booking_id', 'id');
    }
}
