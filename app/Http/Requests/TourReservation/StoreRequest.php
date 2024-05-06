<?php

namespace App\Http\Requests\TourReservation;

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
            "reserved_user_id" => "required|integer|exists:users,id",
            'items' => 'required|array',
            'items.*.user_id' => 'nullable|integer',
            'items.*.tour_id' => 'required|integer',
            'items.*.trip_date' => 'required|date',
            'items.*.type' => 'required|string|in:Guided,DIY,Layover',
            'items.*.ticket_pass' => 'nullable|string',
            'items.*.number_of_pass' => 'required|integer',
            'items.*.amount' => 'required|numeric',
            'items.*.discounted_amount' => 'required|numeric',
            "referral_code" => "nullable",
            "promo_code" => "nullable",
        ];
    }
}
