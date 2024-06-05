<?php

namespace App\Http\Requests;

use App\Http\DTO\PermissionDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreatePermissionRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:permissions',
            'description' => 'required|string',
            'cipher' => 'required|string|max:100|unique:permissions',
        ];
    }

    public function toDTO()
    {
        return new PermissionDTO(
            $this->input('name'),
            $this->input('description'),
            $this->input('cipher'),
            now(),
            Auth::id()
        );
    }
}