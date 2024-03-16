<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\DTO\AuthResourceDTO;

class LoginRequest extends FormRequest
{
    
    protected $stopOnFirstFailure = false; // будет ругаться при первом неудачном входе

    public function authorize()
    {
        return TRUE; // Указываем что авторизация не требуется (как было сказанно в требованиях)
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'regex:/^[A-Z][a-zA-Z]+$/'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]+$/'],
        ];
    }

    public function toAuthResource()
    {
        return new AuthResourceDTO(null); // Здесь можно передать токен, если нужно
    }

}
