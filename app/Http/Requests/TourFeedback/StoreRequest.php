<?php

namespace App\Http\Requests\TourFeedback;

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
            'customer_id' => 'required|exists:users,id',
            'tour_id' => 'required|exists:tours,id',
            'reservation_id' => 'required|exists:tour_reservations,id',
            'message' => 'nullable',
            'category_one_rate' => 'required',
            'category_two_rate' => 'required',
            'category_three_rate' => 'required',
            'category_four_rate' => 'required',
            'category_five_rate' => 'required',
            'category_six_rate' => 'required',
        ];
    }

    public function messages() {
        return [
            'customer_id.required' => 'The customer field is required.',
            'customer_id.exists' => 'The customer should be exists on our records.',
            'tour_id.required' => 'The tour field is required.',
            'tour_id.exists' => 'The tour should be exists on our records.',
            'reservation_id.required' => 'The tour reservation field is required.',
            'reservation_id.exists' => 'The tour reservation should be exists on our records.'
        ];
    }
}
