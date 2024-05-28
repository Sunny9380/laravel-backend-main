<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class City extends Model
{
    use HasFactory;

    use HasSlug;

    protected $table = 'cities';

    protected $fillable = [
        'state_id',
        'name',
        'image',
        'is_stopped'
    ];

    public function properties()
    {
        return $this->hasMany(Hotel::class, 'city_id', 'id');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class, 'city_id', 'id');
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Hotel::class, 'city_id', 'hotel_id', 'id', 'id');
    }

}
