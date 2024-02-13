<?php

namespace App\Http\Requests\Tour;

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
            'price' => 'required',
            'bracket_price_one' => 'required',
            'bracket_price_two' => 'required',
            'bracket_price_three' => 'required',
            'start_time.*' => 'required|required_with:end_time.*|date_format:H:i',
            'end_time.*' => 'required|required_with:start_time.*|date_format:H:i|after:start_time.*',
        ];
    }

    public function messages() {
        return [
            'start_time.*.required' => 'The Start Time field is required.',
            'end_time.*.required' => 'The End Time field is required.',
            'start_time.*.required_with' => 'The Start Time field is required when End Time is present.',
            'end_time.*.required_with' => 'The End Time field is required when Start Time is present.',
            'end_time.*.after' => 'The End Time must be greater than the Start Time.',
        ];
    }
}
