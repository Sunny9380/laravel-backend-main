<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policies extends Model
{
    use HasFactory;

    protected $table = 'policies';

    protected $fillable = [
        'name',
        'policies',
        'is_active'
    ];

    public function policyItems()
    {
        return $this->hasMany(PolicyItem::class, 'policy_id', 'id');
    }
}
