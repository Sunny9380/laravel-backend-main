<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class SupportCategory extends Model
{
    use HasFactory;
    use HasSlug;

    protected $table = 'support_categories';

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function queries()
    {
        return $this->hasMany(SupportQuery::class, 'category_id', 'id');
    }
}
