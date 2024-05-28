<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomsExtraAvailability extends Model
{
    use HasFactory;

    protected $table = 'rooms_extra_availability';

    protected $fillable = [
        'room_id',
        'number_of_rooms',
        'start_date',
        'end_date'
    ];
}
