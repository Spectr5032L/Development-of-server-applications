<?php

namespace App\Http\DTO;

class LoginDTO
{
    public $username;
    public $password;
    public $tfa_code;

    public function __construct($username, $password, $tfa_code)
    {
        $this->username = $username;
        $this->password = $password;
        $this->tfa_code = $tfa_code;
    }
}
