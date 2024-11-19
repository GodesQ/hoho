<?php

namespace App\Http\Requests\TourReservation\v2;

use App\Rules\UserHasFullName;
use App\Rules\ValidUserContactNumber;
use Illuminate\Foundation\Http\FormRequest;

class BookRegisteredSingleReservationsRequest extends FormRequest
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
            "trip_date" => ['required', 'date'],
            "type" => ["required", 'in:DIY,Guided,Seasonal,Transit'],
            "tour_id" => ["required", 'exists:tours,id'],
            "number_of_pass" => ["required", 'integer'],
            "payment_method" => ["required", 'in:cash,aqwire'],
            "promo_code" => ["required", 'exists:promo_codes,code'],
            "referral_code" => ["required", 'exists:referrals,referral_code'],
            "amount" => ["required", 'numeric'],
            "discounted_amount" => ["required", 'numeric'],
            "arrival_datetime" => ["required_if:type,Transit"],
            "departure_datetime" => ["required_if:type,Transit"],
            "flight_from" => ["required_if:type,Transit"],
            "flight_to" => ["required_if:type,Transit"],
            "passport_number" => ["required_if:type,Transit"],
            "has_insurance" => ["required_if:type,Transit"],
            "type_of_plan" => ["required_if:type,Transit"],
        ];
    }
}
