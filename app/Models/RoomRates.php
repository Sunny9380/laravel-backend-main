<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomRates extends Model
{
    use HasFactory;

    protected $table = 'room_rates';

    protected $fillable = [
        'room_id',
        'rate',
        'start_date',
        'end_date'
    ];

    public function getRates(){
        return $this->hasMany(RoomRates::class, 'room_id', 'id')->get();
    }
}
