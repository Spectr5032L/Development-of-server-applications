<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\DTO\RegisterDTO;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'required|string|regex:/[A-Z][a-zA-Z]{6,}$/|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+]).{8,}$/',
            'c_password' => 'required|string|same:password',
            'birthday' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function toDTO()
    {
        return new RegisterDTO(
            $this->input('username'),
            $this->input('email'),
            $this->input('password'),
            $this->input('c_password'),
            $this->input('birthday')
        );
    }
}

