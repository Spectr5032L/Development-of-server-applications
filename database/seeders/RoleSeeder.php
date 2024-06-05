<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Administrator role',
                'cipher' => 'ADMIN',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
            [
                'name' => 'User',
                'description' => 'User role',
                'cipher' => 'USER',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
            [
                'name' => 'Guest',
                'description' => 'Guest role',
                'cipher' => 'GUEST',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}