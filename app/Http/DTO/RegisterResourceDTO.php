<?php

namespace App\Http\DTO;

class RegisterResourceDTO
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
