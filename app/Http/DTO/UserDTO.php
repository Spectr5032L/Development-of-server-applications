<?php

namespace App\Http\DTO;

use App\Models\User;

class UserDTO
{
    public $username;
    public $email;
    public $password;
    public $birthday;
    public $roles;

    public function __construct(array $data)
    {
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->birthday = $data['birthday'];
        $this->roles = $data['roles'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'birthday' => $this->birthday,
            'roles' => $this->roles,
        ];
    }
    public static function fromModelToDTO(User $user): self
    {
        return new self([
            'username' => $user->username,
            'email' => $user->email,
            'password' => $user->password,
            'birthday' => $user->birthday,
            'roles' => $user->roles()->roles,]
        );
    }
}