<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyRequest extends Model
{
    use HasFactory;

    protected $table = 'list_property_requests';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'type',
        'gst_number',
        'address',
        'password'
    ];
}
