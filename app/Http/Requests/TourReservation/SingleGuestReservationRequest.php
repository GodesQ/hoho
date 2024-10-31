<?php

namespace App\Http\Requests\TourReservation;

use Illuminate\Foundation\Http\FormRequest;

class SingleGuestReservationRequest extends FormRequest
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
            "trip_date" => ["required"],
            "type" => ["required", "in:Guided,Transit,Seasonal,DIY"],
            "tour_id" => ['required'],
            "number_of_pax" => ["required", "integer"],
            "ticket_pass" => ["nullable"],
            "payment_method" => ['required', 'in:aqwire'],
            "promo_code" => ["nullable"],
            "referral_code" => ["nullable"],
            "amount" => ["required", "numeric"],
            "discounted_amount" => ["required", "numeric"],
            "arrival_datetime" => ["nullable"],
            "departure_datetime" => ["nullable"],
            "flight_from" => ["nullable"],
            "flight_to" => ["nullable"],
            "passport_number" => ['nullable'],
            "has_insurance" => ["nullable", "boolean"],
            "type_of_plan" => ["nullable", "integer", "in:1,2,3"],
            "total_insurance_amount" => ["nullable", "numeric"],
            "firstname" => ["required"],
            "lastname" => ["required"],
            "middlename" => ["nullable"],
            "email" => ["required", "email"],
            "contact_no" => ["required"],
            "address" => ["nullable"]
        ];
    }
}
