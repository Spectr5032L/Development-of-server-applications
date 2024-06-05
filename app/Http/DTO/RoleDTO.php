<?php

namespace App\Http\DTO;

use App\Models\Role;

class RoleDTO
{
    public $name;
    public $description;
    public $cipher;
    public $created_at;
    public $created_by;
    public $deleted_at;
    public $deleted_by;
    public $permissions;

    public function __construct($name, $description, $cipher, $created_at, $created_by, $deleted_at = null, $deleted_by = null, $permissions = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->cipher = $cipher;
        $this->created_at = $created_at;
        $this->created_by = $created_by;
        $this->deleted_at = $deleted_at;
        $this->deleted_by = $deleted_by;
        $this->permissions = $permissions;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'cipher' => $this->cipher,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'permissions' => $this->permissions,
        ];
    }
    public static function fromModelToDTO(Role $role): self
    {
        return new self(
            $role->name,
            $role->description,
            $role->cipher,
            $role->created_at,
            $role->created_by,
            $role->deleted_at,
            $role->deleted_by,
            $role->permissions()->permissions
        );
    }
}