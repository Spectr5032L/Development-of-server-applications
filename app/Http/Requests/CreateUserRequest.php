<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\DTO\UserDTO;
use Illuminate\Support\Facades\Auth;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'username' => 'required|string|regex:/[A-Z][a-zA-Z]{6,}$/|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+]).{8,}$/',
            'birthday' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function toDTO()
    {
        return new UserDTO([
            'username' => $this->input('username'),
            'email' => $this->input('email'),
            'password' => $this->input('password'),
            'birthday' => $this->input('birthday'),
        ]);
    }

    public function messages()
    {
        return [
            'username.required' => 'Имя пользователя обязательно для заполнения',
            'username.string' => 'Имя пользователя должно быть строкой',
            'username.regex' => 'Имя пользователя должно начинаться с заглавной буквы и содержать только буквы латинского алфавита',
            'username.unique' => 'Пользователь с таким именем уже существует',
            'email.required' => 'Email обязателен для заполнения',
            'email.string' => 'Email должен быть строкой',
            'email.email' => 'Email должен быть действительным адресом электронной почты',
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.required' => 'Пароль обязателен для заполнения',
            'password.string' => 'Пароль должен быть строкой',
            'password.min' => 'Пароль должен содержать не менее 8 символов',
            'password.regex' => 'Пароль должен содержать не менее 8 символов, как минимум одну цифру, одну заглавную букву, одну строчную букву и один специальный символ',
            'birthday.required' => 'Дата рождения обязательна для заполнения',
            'birthday.date' => 'Дата рождения должна быть датой',
            'birthday.date_format' => 'Дата рождения должна соответствовать формату ГГГГ-ММ-ДД',
        ];
    }
}