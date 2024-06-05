<?php

namespace App\Http\Requests;

use App\Http\DTO\RoleDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'name' => 'nullable|string|max:100|unique:roles',
            'description' => 'nullable|string',
            'cipher' => 'nullable|string|max:100|unique:roles',
        ];
    }

    public function toDTO()
    {
        return new RoleDTO(
            $this->input('name'),
            $this->input('description'),
            $this->input('cipher'),
            now(),
            Auth::id()
        );
    }
}