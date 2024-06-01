<?php

namespace App\Http\DTO;

class RegisterDTO
{
    public $username;
    public $email;
    public $password;
    public $c_password;
    public $birthday;

    public function __construct($username, $email, $password, $c_password, $birthday)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->c_password = $c_password;
        $this->birthday = $birthday;
    }
}
