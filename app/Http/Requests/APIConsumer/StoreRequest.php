<?php

namespace App\Http\Requests\APIConsumer;

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
            'consumer_name' => 'required|max:20',
            'platform' => 'required|max:50',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|max:20',   
        ];
    }
}
