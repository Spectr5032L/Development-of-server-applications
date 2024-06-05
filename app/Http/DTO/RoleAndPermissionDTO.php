<?php

namespace App\Http\DTO;

use App\Models\RolesAndPermissions;

class RoleAndPermissionDTO
{
    public $role_id;
    public $permission_id;
    public $created_at;
    public $created_by;
    public $deleted_at;
    public $deleted_by;

    public function __construct($role_id, $permission_id, $created_at, $created_by, $deleted_at = null, $deleted_by = null)
    {
        $this->role_id = $role_id;
        $this->permission_id = $permission_id;
        $this->created_at = $created_at;
        $this->created_by = $created_by;
        $this->deleted_at = $deleted_at;
        $this->deleted_by = $deleted_by;
    }

    public function toArray(): array
    {
        return [
            'role_id' => $this->role_id,
            'permission_id' => $this->permission_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ];
    }
    public static function fromModelToDTO(RolesAndPermissions $roleAndPermission): self
    {
        return new self(
            $roleAndPermission->role_id,
            $roleAndPermission->permission_id,
            $roleAndPermission->created_at,
            $roleAndPermission->created_by,
            $roleAndPermission->deleted_at,
            $roleAndPermission->deleted_by
        );
    }
}