<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\DTO\RegisterResourceDTO;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Указываем что авторизация не требуется (как было сказанно в требованиях)
    }

    public function rules()
    {
        return [
            'username' => ['required', 'string', 'regex:/^[A-Z][a-zA-Z]+$/','unique:users'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]+$/'],
            'c_password' => ['required', 'string', 'same:password'],
            'birthday' => ['required', 'date', 'date_format:Y-m-d'],
        ];
    }

    public function toRegisterResource()
    {
        return new RegisterResourceDTO(null); // Здесь можно передать данные нового пользователя, если нужно
    }

}
