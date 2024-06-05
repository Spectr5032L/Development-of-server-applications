<?php

namespace App\Http\DTO;

use App\Models\Permission;

class PermissionCollectionDTO
{
    public $permissions;

    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }

    public static function fromCollectionToDTO($permissions) : self
    {
        $permissionDTOs = [];

        foreach ($permissions as $permission)
        {
            $permissionDTOs[] = PermissionDTO::fromModelToDTO($permission);
        }

        return new self($permissionDTOs);
    }
}