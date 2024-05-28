<?php

//use Spatie\Sluggable\SlugOptions;
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Hotel extends Model
{
    use HasSlug;
    use HasFactory;

    protected $table = 'hotels';

    protected $fillable = [
        'vendor_id',
        'name',
        'slug',
        'description',
        'policies',
        'primary_number',
        'secondary_number',
        'primary_email',
        'secondary_email',
        'property_type_id',
        'address',
        'city_id',
        'nearby_locations',
        'country',
        'zip',
        'location_iframe',
        'amenities',
        'banner_image',
        'tenancy_agreement',
        'corporate_documents',
        'identity_documents',
        'proof_of_ownership',
        'isVerified',
        'isBanned',
        'isActive',
    ];

    public function wishlist()
    {
        return $this->hasOne(Wishlist::class, 'hotel_id', 'id');
    }

    public function hotel_available_time()
    {
        return $this->hasOne(PropertyAvailabilityTime::class, 'property_id', 'id');
    }


    public function hotel_galleries()
    {
        return $this->hasMany(HotelGallery::class, 'hotel_id', 'id');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getBookingType()
    {
        return $this->hasManyThrough(BookingType::class, HotelTypeBookingOptions::class, 'hotel_id', 'id', 'id', 'booking_type_id');

    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    //same as above (vendor()) but name different
    public function getVendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    public function getHotelAvailability()
    {
        return $this->hasOne(PropertyAvailabilityTime::class, 'property_id', 'id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'hotel_id', 'id');
    }

    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function amenities()
    {
        //this gives the id of the amenity from the pivot table we will get the amenity name from the amenities table
        return $this->hasManyThrough(Amenity::class, PropertyAmenities::class, 'property_id', 'id', 'id', 'amenity_id');
    }
}
