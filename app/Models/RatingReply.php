<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingReply extends Model
{
    use HasFactory;

    protected $table = 'rating_reply';

    protected $fillable = [
        'rating_id',
        'user_id',
        'reply'
    ];
}
