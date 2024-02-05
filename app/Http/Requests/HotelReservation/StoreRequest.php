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
            'number_of_pax' => 'required|numeric',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'status' => 'nullable|in:pending,declined,approved'
        ];
    }
}
