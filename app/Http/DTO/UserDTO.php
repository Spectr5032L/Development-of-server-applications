<?php

namespace App\Http\DTO;

class UserDTO
{
    public $username;
    public $email;
    public $password;
    public $birthday;

    public function __construct($username, $email, $password, $birthday)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->birthday = $birthday;
    }

}
