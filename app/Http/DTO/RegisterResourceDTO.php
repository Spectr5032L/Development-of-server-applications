<?php

namespace App\Http\DTO;

use Carbon\Carbon;

class RegisterResourceDTO
{
    public $fio;
    public $email;
    public $date;

    public function __construct($user)
    {
        $this->fio = $user->name;
        $this->email = $user->email;
        $this->date = Carbon::parse($user->birthday)->format('d.m.Y');
    }
}
