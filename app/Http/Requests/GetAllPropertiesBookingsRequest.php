<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetAllPropertiesBookingsRequest extends FormRequest
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
    public function rules()
    {
        return [
            'bookingType' => 'nullable|string|in:upcoming,ongoing,past,cancelled',
            'search' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'bookingType.in' => 'The booking type must be one of the following: upcoming, ongoing, past, cancelled.',
            'search.max' => 'The search term cannot be longer than 255 characters.',
        ];
    }
}
