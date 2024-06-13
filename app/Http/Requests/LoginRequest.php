<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\DTO\LoginDTO;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'required|string|regex:/[A-Z][a-zA-Z]{6,}$/',
            'password' => 'required|string|min:8|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+]).{8,}$/',
            'tfa_code' => 'required|digits:6',
        ];
    }

    public function toDTO()
    {
        return new LoginDTO(
            $this->input('username'),
            $this->input('password'),
            $this->input('tfa_code')
        );

    }
}

