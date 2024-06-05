<?php

namespace App\Http\Requests;

use App\Http\DTO\RoleAndPermissionDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRoleAndPermissionRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'role_id' => 'nullable|integer|exists:roles,id',
            'permission_id' => 'nullable|integer|exists:permissions,id',
        ];
    }

    public function toDTO()
    {
        return new RoleAndPermissionDTO(
            $this->input('role_id'),
            $this->input('permission_id'),
            now(),
            Auth::id()
        );
    }
}