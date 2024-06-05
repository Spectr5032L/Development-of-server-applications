<?php

namespace App\Http\DTO;

class UserCollectionDTO
{
    public $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public static function fromCollectionToDTO($users) : self
    {
        $userDTOs = [];

        foreach ($users as $user)
        {
            $userDTOs[] = UserDTO::fromModelToDTO($user);
        }

        return new self($userDTOs);
    }
}