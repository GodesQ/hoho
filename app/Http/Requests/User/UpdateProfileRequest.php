<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                new \App\Rules\UniqueAcrossTables('users', 'admins', 'email')
            ],
            'firstname' => 'nullable',
            'lastname' => 'nullable',
            'birthdate' => 'nullable'
        ];
    }
}
