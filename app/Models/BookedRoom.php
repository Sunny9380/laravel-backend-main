<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedRoom extends Model
{
    use HasFactory;

    protected $table = 'booked_rooms';

    protected $fillable = [
        'booking_id',
        'room_id',
        'room_count',
        'guest_count'
    ];
}
