<?php

namespace App\Http\Requests\HotelReservation;

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
            'reserved_user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'adult_quantity' => 'required|numeric',
            'children_quantity' => 'required|numeric',
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after:checkin_date',
            'children_age' => 'required_if:children_quantity,>,1|array',
            'status' => 'nullable|in:pending,declined,approved'
        ];
    }
}
