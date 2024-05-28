<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyItem extends Model
{
    use HasFactory;

    protected $table = 'policy_item';

    protected $fillable = [
        'policy_id',
        'policy',
        'is_active'
    ];
}
