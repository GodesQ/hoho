<?php

namespace App\Http\Requests\TourReservation\v2;

use App\Rules\ValidUserContactNumber;
use Illuminate\Foundation\Http\FormRequest;

class BookUnregisteredMultipleReservationsRequest extends FormRequest
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
            "referral_code" => ["nullable", "exists:referrals,referral_code"],
            "promocode" => ["nullable", "exists:promo_codes,code"],
            "items" => ['required', 'array'],
            "items.*.tour_id" => ["required", "exists:tours,id"],
            "items.*.trip_date" => ["required", 'date'],
            "items.*.type" => ["required", 'in:DIY,Guided,Seasonal,Transit'],
            "items.*.ticket_pass" => ["nullable"],
            "items.*.number_of_pass" => ["required", 'numeric'],
            "items.*.amount" => ["required", 'numeric'],
            "items.*.discounted_amount" => ["required", 'numeric'],
            "items.*.type_of_plan" => ["nullable", 'numeric'],
            "items.*.has_insurance" => ["nullable"],
        ];
    }
}
