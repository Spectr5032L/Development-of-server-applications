<?php

namespace App\Http\DTO;

class UserResourceDTO
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
