<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingType extends Model
{
    use HasFactory;

    protected $table = 'booking_types';

    protected $fillable = [
        'name',
        'description',
        'MinTime',
        'MaxTime',
        'is_active'
    ];
}
