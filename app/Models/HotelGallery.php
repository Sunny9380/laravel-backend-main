<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelGallery extends Model
{
    use HasFactory;

    protected $table = 'hotel_galleries';

    protected $fillable= [
        'hotel_id',
        'image',
    ];

    public function hotel(){
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }
}
