<?php

namespace App\Http\DTO;

class UserAndRoleCollectionDTO
{
    public $userAndRoles;

    public function __construct(array $userAndRoles)
    {
        $this->userAndRoles = $userAndRoles;
    }

    public static function fromCollectionToDTO($userAndRoles) : self
    {
        $userAndRoleDTOs = [];

        foreach ($userAndRoles as $userAndRole)
        {
            $userAndRoleDTOs[] = UserAndRoleDTO::fromModelToDTO($userAndRole);
        }

        return new self($userAndRoleDTOs);
    }
}