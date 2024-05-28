<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyAvailabilityTime extends Model
{
    use HasFactory;

    protected $table = 'property_availability_time';

    protected $fillable = [
        'property_id',
        'start_time',
        'end_time',
    ];
}
