<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopReviews extends Model
{
    use HasFactory;

    protected $table = 'top_reviews';

    protected $fillable = [
        'review_id',
        'rating',
    ];

    public function ratings()
    {
        return $this->belongsTo(Rating::class, 'review_id', 'id');
    }
}
