<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password'     => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            if ($user && ! Hash::check($this->input('current_password'), $user->password)) {
                $validator->errors()->add('current_password', 'Current password is incorrect.');
            }
        });
    }

    public function messages()
    {
        return [
            'new_password.confirmed' => 'New password and confirmation do not match.',
            'new_password.min'       => 'New password must be at least 8 characters.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        parent::failedValidation($validator);
    }
}
