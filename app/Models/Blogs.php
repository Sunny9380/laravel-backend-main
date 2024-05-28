<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Blogs extends Model
{
    use HasFactory;
    use HasSlug;

    protected $table = 'blogs';
    protected $fillable = [
        'title',
        'image',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'body',
        'is_active'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

}
