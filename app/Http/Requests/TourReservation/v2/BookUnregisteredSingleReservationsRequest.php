<?php

namespace App\Http\Requests\TourReservation\v2;

use App\Rules\ValidUserContactNumber;
use Illuminate\Foundation\Http\FormRequest;

class BookUnregisteredSingleReservationsRequest extends FormRequest
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
            "firstname" => ['required', 'max:50'],
            "lastname" => ['required', 'max:50'],
            "email" => ['required', 'email'],
            "contact_no" => ['required', new ValidUserContactNumber()],
            "address" => ['nullable'],
            "trip_date" => ['required', 'date'],
            "type" => ["required", 'in:DIY,Guided,Seasonal,Transit'],
            "tour_id" => ["required", 'exists:tours,id'],
            "number_of_pass" => ["required", 'integer', 'min:1'],
            "payment_method" => ["required", 'in:cash,aqwire'],
            "promo_code" => ["nullable", 'exists:promo_codes,code'],
            "referral_code" => ["nullable", 'exists:referrals,referral_code'],
            "amount" => ["required", 'numeric'],
            "discounted_amount" => ["required", 'numeric'],
            "arrival_datetime" => ["required_if:type,Transit"],
            "departure_datetime" => ["required_if:type,Transit"],
            "flight_from" => ["required_if:type,Transit"],
            "flight_to" => ["required_if:type,Transit"],
            "passport_number" => ["required_if:type,Transit"],
            "has_insurance" => ["nullable"],
            "type_of_plan" => ["nullable"],
        ];
    }
}
