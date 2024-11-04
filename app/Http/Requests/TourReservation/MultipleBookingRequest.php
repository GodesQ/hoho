<?php

namespace App\Http\Requests\TourReservation;

use Illuminate\Foundation\Http\FormRequest;

class MultipleBookingRequest extends FormRequest
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
            'reserved_user_id' => ['required', 'exists:users,id'],
            'items' => ['array'],
            'items.*.user_id' => ['required', 'exists:users,id'],
            'items.*.tour_id' => ['required', 'exists:tours,tour_id'],
            'items.*.trip_date' => ['required', 'date'],
            'items.*.type' => ['required', 'in:DIY'],
            'items.*.ticket_pass' => ['nullable'],
            'items.*.number_of_pass' => ['required'],
            'items.*.amount' => ['required', 'numeric'],
            'items.*.discounted_amount' => ['required', 'numeric'],
            'items.*.type_of_plan' => ['nullable', 'integer'],
            'items.*.has_insurance' => ['nullable'],
            'items.*.total_insurance_amount' => ['nullable', 'numeric'],
        ];
    }
}
