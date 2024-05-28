<?php

namespace Database\Factories;

use App\Models\PropertyType;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::query()->inRandomOrder()->first()->id,
            'name' => fake()->name,
            'slug' => fake()->slug,
            'description' => fake()->text,
            'primary_number' => fake()->phoneNumber,
            'secondary_number' => fake()->phoneNumber,
            'primary_email' => fake()->email,
            'secondary_email' => fake()->email,
            'property_type_id' => PropertyType::query()->inRandomOrder()->first()->id,
            'address' => fake()->address,
            'city_id' => fake()->numberBetween(1, 5),
            'country' => fake()->country,
            'zip' => fake()->postcode,
            'location_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3357.3391663216466!2d74.87510507500285!3d32.70361408773487!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391e83577b2174e7%3A0x525a2d0dfc8e1018!2sLemon%20Tree%20Hotel%2C%20Jammu!5e0!3m2!1sen!2sin!4v1697887771318!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
            'amenities' => "Toiletries, Mini fridge, Safe, Free Wi-Fi, Hair dryer, Ironing board, Coffee/tea maker, Air conditioning, Flat-screen TV, Satellite TV, Telephone, Desk, Wake-up service",
            //inserting array of images in gallery
            'gallery' => fake()->text,
            'tenancy_agreement' => fake()->text,
            'corporate_documents' => fake()->text,
            'identity_documents' => fake()->text,
            'proof_of_ownership' => fake()->text,
        ];
    }
}
