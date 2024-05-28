<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelTypeBookingOptions extends Model
{
    use HasFactory;

    protected $table = 'hotel_type_booking_options';

    protected $fillable = [
        'hotel_id',
        'booking_type_id'
    ];
}
