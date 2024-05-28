<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PropertyType extends Model
{
    use HasSlug;
    use HasFactory;

    protected $table = 'property_types';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active'
    ];

    public function properties()
    {
        return $this->hasMany(Hotel::class, 'property_type_id', 'id');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

}
