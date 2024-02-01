<?php

namespace App\Http\Requests\MerchantStore;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            "name" => "required",
            "code" => "nullable",
            "type" => "required|in:Hotel,Restaurant,Tour Provider,Store",
            "featured_image" => "nullable|image",
            "nature_of_business" => "nullable",
            "organization_id" => "required",
            "address" => "nullable",
            "latitude" => "nullable",
            "longitude" => "nullable",
            "description" => "nullable",
            "interests" => "nullable|array",
            "payment_options" => "nullable",
            "contact_number" => "nullable|max:12",
            "contact_email" => "nullable|email",
            "business_hours" => "nullable",
            "tags" => "nullable",
            "brochure" => "nullable|mimes:pdf,ppt,pptx|max:2048"
        ];
    }
}
