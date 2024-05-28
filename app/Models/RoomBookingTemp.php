<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomBookingTemp extends Model
{
    use HasFactory;

    protected $table = 'room_booking_temps';

    protected $fillable = [
        'user_id',
        'room_id',
        'room_count',
    ];
}
