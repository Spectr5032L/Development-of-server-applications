<?php

namespace App\Http\DTO;

class AuthResourceDTO
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }
}
