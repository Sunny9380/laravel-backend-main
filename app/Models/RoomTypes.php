<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class RoomTypes extends Model
{
    use HasFactory;
    use HasSlug;

    protected $table='room_types';

    protected $fillable = [
        'name',
        'description',
        'image',
        'status',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'room_type_id', 'id');
    }

}
