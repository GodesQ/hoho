<?php

namespace App\Http\Requests\TourReservation\v2;

use App\Rules\UserHasFullName;
use App\Rules\ValidUserContactNumber;
use Illuminate\Foundation\Http\FormRequest;

class BookRegisteredMultipleReservationsRequest extends FormRequest
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
            "reserved_user_id" => ["required", "exists:users,id", new UserHasFullName(), new ValidUserContactNumber(true)],
            "items" => ["required", "array"],
            "items.*.tour_id" => ["required", "exists:tours,id"],
            "items.*.trip_date" => ["required", 'date'],
            "items.*.type" => ["required", 'in:DIY,Guided,Seasonal,Transit'],
            "items.*.ticket_pass" => ["nullable"],
            "items.*.number_of_pass" => ["required", 'numeric'],
            "items.*.amount" => ["required", 'numeric'],
            "items.*.discounted_amount" => ["required", 'numeric'],
            "items.*.type_of_plan" => ["nullable", 'numeric'],
            "items.*.has_insurance" => ["nullable"],
            "items.*.referral_code" => ["nullable", "exists:referrals,referral_code"],
            "items.*.promo_code" => ["nullable", "exists:promo_codes,code"],
        ];
    }

    public function messages()
    {
        return [
            'reserved_user_id.required' => 'The reserved user ID is required.',
            'reserved_user_id.exists' => 'The reserved user ID must exist in the users table.',
            'items.required' => 'The items array is required.',
            'items.array' => 'The items field must be an array.',
            'items.*.tour_id.required' => 'Each item must include a valid tour ID.',
            'items.*.tour_id.exists' => 'The tour ID in an item must exist in the tours table.',
            'items.*.trip_date.required' => 'Each item must include a trip date.',
            'items.*.trip_date.date' => 'The trip date in an item must be a valid date.',
            'items.*.type.required' => 'Each item must specify the type of tour.',
            'items.*.type.in' => 'The tour type must be one of the following: DIY, Guided, Seasonal, Transit.',
            'items.*.number_of_pass.required' => 'Each item must specify the number of passes.',
            'items.*.number_of_pass.numeric' => 'The number of passes must be a numeric value.',
            'items.*.amount.required' => 'Each item must include an amount.',
            'items.*.amount.numeric' => 'The amount must be a numeric value.',
            'items.*.discounted_amount.required' => 'Each item must include a discounted amount.',
            'items.*.discounted_amount.numeric' => 'The discounted amount must be a numeric value.',
            'items.*.type_of_plan.numeric' => 'The type of plan must be a numeric value.',
            'items.*.referral_code.exists' => 'The referral code must exist in the referrals table.',
            'items.*.promo_code.exists' => 'The promo code must exist in the promo codes table.',
        ];
    }
}
