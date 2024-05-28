<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomHourlyRate extends Model
{
    use HasFactory;

    protected $table = 'room_hourly_rate';

    protected $fillable = [
        'room_id',
        'is_percent',
        '_3_hr',
        '_4_hr',
        '_5_hr',
        '_6_hr',
        '_7_hr',
        '_8_hr',
        '_9_hr',
        '_10_hr',
        '_11_hr',
        '_12_hr'
    ];

    
}
