<?php

namespace App\Http\DTO;

class RoleAndPermissionCollectionDTO
{
    public $roleAndPermissions;

    public function __construct(array $roleAndPermissions)
    {
        $this->roleAndPermissions = $roleAndPermissions;
    }

    public static function fromCollectionToDTO($roleAndPermissions) : self
    {
        $roleAndPermissionDTOs = [];

        foreach ($roleAndPermissions as $roleAndPermission)
        {
            $roleAndPermissionDTOs[] = RoleAndPermissionDTO::fromModelToDTO($roleAndPermission);
        }

        return new self($roleAndPermissionDTOs);
    }
}