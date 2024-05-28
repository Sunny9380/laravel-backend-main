<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportHelpline extends Model
{
    use HasFactory;

    protected $table = 'support_helplines';

    protected $fillable = [
        'title',
        'mail',
        'phone'
    ];
}
