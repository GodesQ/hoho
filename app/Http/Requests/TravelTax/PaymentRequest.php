<?php

namespace App\Http\Requests\TravelTax;

use App\Rules\ValidatePassenger;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'processing_fee' => 'required|numeric',
            'discount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'passengers' => 'array',
            'passengers.*.firstname' => 'required|string|max:50',
            'passengers.*.lastname' => 'required|string|max:50',
            'passengers.*.middlename' => 'nullable|string|max:50',
            'passengers.*.suffix' => 'nullable|string|max:50',
            'passengers.*.passport_number' => 'required|string|max:50',
            'passengers.*.ticket_number' => 'required|string|max:50',
            'passengers.*.class' => 'required|string|max:50',
            'passengers.*.mobile_number' => 'required|string|min:12|max:20',
            'passengers.*.email_address' => 'required|email|max:50',
            'passengers.*.destination' => 'required|string|max:50',
            'passengers.*.departure_date' => 'required|max:50',
            'passengers.*.passenger_type' => 'required|string|max:50',
            'passengers.*.amount' => 'required|numeric',
        ];
    }
}
