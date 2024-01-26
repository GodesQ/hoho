<?php

namespace App\Http\Requests\RestaurantReservation;

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
            'seats' => 'required|numeric|min:1',
            'reservation_date' => 'required',
            'reservation_time' => 'required',
            'status' => 'required|in:pending,declined,approved',
        ];
    }
}
