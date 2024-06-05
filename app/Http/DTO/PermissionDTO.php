<?php

namespace App\Http\DTO;

use App\Models\Permission;

class PermissionDTO
{
    public $name;
    public $description;
    public $cipher;
    public $created_at;
    public $created_by;
    public $deleted_at;
    public $deleted_by;

    public function __construct($name, $description, $cipher, $created_at, $created_by, $deleted_at = null, $deleted_by = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->cipher = $cipher;
        $this->created_at = $created_at;
        $this->created_by = $created_by;
        $this->deleted_at = $deleted_at;
        $this->deleted_by = $deleted_by;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'cipher' => $this->cipher,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ];
    }
    public static function fromModelToDTO(Permission $permission): self
    {
        return new self(
            $permission->name,
            $permission->description,
            $permission->cipher,
            $permission->created_at,
            $permission->created_by,
            $permission->deleted_at,
            $permission->deleted_by
        );
    }
}