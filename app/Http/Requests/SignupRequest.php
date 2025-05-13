<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|max:255|unique:users',
            'name' => 'required|min:2|max:255',
            'password' => 'required|min:6',
        ];
    }
}