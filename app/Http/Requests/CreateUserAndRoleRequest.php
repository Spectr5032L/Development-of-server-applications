<?php

namespace App\Http\Requests;

use App\Http\DTO\UserAndRoleDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateUserAndRoleRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'role_id' => 'required|integer|exists:roles,id',
        ];
    }

    public function toDTO()
    {
        return new UserAndRoleDTO(
            $this->input('user_id'),
            $this->input('role_id'),
            now(),
            Auth::id()
        );
    }
}