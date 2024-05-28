<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $table = 'ratings';

    protected $fillable = [
        'hotel_id',
        'user_id',
        'rating',
        'review',
        'status'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function ratingReply()
    {
        return $this->hasMany(RatingReply::class);
    }
}
