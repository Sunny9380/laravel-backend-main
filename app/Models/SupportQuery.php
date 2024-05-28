<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportQuery extends Model
{
    use HasFactory;

    protected $table = "support_queries";

    protected $fillable = [
        'question',
        'category_id',
        'answer',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(SupportCategory::class, 'category_id');
    }
}
