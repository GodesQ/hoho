<?php

namespace App\Http\Requests\Attraction;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => ["required"],
            "tour_provider_id" => ["nullable"],
            "featured_image" => ["required", "image"],
            "interests" => ["nullable", "array"],
            "product_categories" => ["nullable", "array"],
            "price" => ["nullable", "numeric"],
            "contact_no" => ["nullable"],
            "youtube_id" => ["nullable"],
            "address" => ["required"],
            "latitude" => ["required"],
            "longitude" => ["required"],
            "description" => ["nullable", "max:500"],
            "operating_hours" => ["nullable"],
            "organization_id" => ["required"],
            "is_cancellable" => ["nullable"],
            "is_refundable" => ["nullable"],
            "is_featured" => ["nullable"],
            "is_active" => ["nullable"],
            "nearest_attraction_ids" => ["nullable", "array"],
            "nearest_store_ids" => ["nullable", "array"],
            "nearest_restaurant_ids" => ["nullable", "array"],
            "nearest_hotel_ids" => ["nullable", "array"],
        ];
    }
}
