<?php

namespace App\Http\Requests\Hotel;

use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'propertyType' => 'required|integer',
            'bookingType' => 'required|string', // Assuming bookingType should be a string
            'policies' => 'required|string',
            'address' => 'required|string',
            'primary_number' => 'required|string|max:255',
            'secondary_number' => 'required|string|max:255',
            'primary_email' => 'required|string|email|max:255',
            'secondary_email' => 'required|string|email|max:255',
            'city_id' => 'required|integer',
            'country' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'location_iframe' => 'required|string',
            'locations' => 'required|string',
            'amenities' => 'required|string',
            'banner_image' => 'required|image|mimes:jpeg,png,jpg|max:500',
            'gallery' => 'required|array',
            'tenancyAgreement' => 'required|array',
            'corporateDocument' => 'required|array',
            'identityDocuments' => 'required|array',
            'addressDocuments' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'vendor_id.required' => 'The vendor ID is required.',
            'vendor_id.exists' => 'The selected vendor ID is invalid.',
            'name.required' => 'The name is required.',
            'name.max' => 'The name must not exceed 255 characters.',
            'description.required' => 'The description is required.',
            'propertyType.required' => 'The property type is required.',
            'propertyType.integer' => 'The property type must be an integer.',
            'bookingType.required' => 'The booking type is required.',
            'policies.required' => 'The policies are required.',
            'address.required' => 'The address is required.',
            'primary_number.required' => 'The primary number is required.',
            'primary_number.max' => 'The primary number must not exceed 255 characters.',
            'secondary_number.required' => 'The secondary number is required.',
            'secondary_number.max' => 'The secondary number must not exceed 255 characters.',
            'primary_email.required' => 'The primary email is required.',
            'primary_email.email' => 'The primary email must be a valid email address.',
            'primary_email.max' => 'The primary email must not exceed 255 characters.',
            'secondary_email.required' => 'The secondary email is required.',
            'secondary_email.email' => 'The secondary email must be a valid email address.',
            'secondary_email.max' => 'The secondary email must not exceed 255 characters.',
            'city_id.required' => 'The city ID is required.',
            'city_id.integer' => 'The city ID must be an integer.',
            'country.required' => 'The country is required.',
            'country.max' => 'The country must not exceed 255 characters.',
            'zip_code.required' => 'The zip code is required.',
            'zip_code.max' => 'The zip code must not exceed 255 characters.',
            'location_iframe.required' => 'The location iframe is required.',
            'locations.required' => 'The locations are required.',
            'amenities.required' => 'The amenities are required.',
            'banner_image.required' => 'The banner image is required.',
            'banner_image.image' => 'The banner image must be an image.',
            'banner_image.mimes' => 'The banner image must be a file of type: jpeg, png, jpg.',
            'banner_image.max' => 'The banner image must not be greater than 500 kilobytes.',
            'gallery.required' => 'The gallery is required.',
            'gallery.array' => 'The gallery must be an array.',
            'tenancyAgreement.required' => 'The tenancy agreement is required.',
            'tenancyAgreement.array' => 'The tenancy agreement must be an array.',
            'corporateDocument.required' => 'The corporate document is required.',
            'corporateDocument.array' => 'The corporate document must be an array.',
            'identityDocuments.required' => 'The identity documents are required.',
            'identityDocuments.array' => 'The identity documents must be an array.',
            'addressDocuments.required' => 'The address documents are required.',
            'addressDocuments.array' => 'The address documents must be an array.',
        ];
    }
}
