<?php

namespace App\Http\DTO;

class RoleCollectionDTO
{
    public $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public static function fromCollectionToDTO($roles) : self
    {
        $roleDTOs = [];

        foreach ($roles as $role)
        {
            $roleDTOs[] = RoleDTO::fromModelToDTO($role);
        }

        return new self($roleDTOs);
    }
}