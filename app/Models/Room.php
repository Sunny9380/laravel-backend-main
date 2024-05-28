<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'hotel_id',
        'availability',
        'is_active',
        'room_type',
        'meal_options',
        'room_size',
        'default_rate',
        'guest_charge',
        'bed_size',
        'is_smoking',
        'is_pet_allowed'
    ];

    public function rates()
    {
        return $this->hasMany(RoomRates::class, 'room_id', 'id');
    }

    public function hourlyRates()
    {
        $this->hasMany(RoomHourlyRate::class, 'room_id', 'id');
    }
}
