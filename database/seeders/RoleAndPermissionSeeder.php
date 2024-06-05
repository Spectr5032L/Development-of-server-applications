<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $userRole = Role::where('name', 'User')->first();
        $guestRole = Role::where('name', 'Guest')->first();

        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            DB::table('roles_and_permissions')->insert
            ([
                'role_id' => $adminRole->id,
                'permission_id' => $permission->id,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $userPermissions = Permission::whereIn('name', ['get-list-user', 'read-user', 'update-user',])->get();

        foreach ($userPermissions as $permission)
        {
            DB::table('roles_and_permissions')->insert
            ([
                'role_id' => $userRole->id,
                'permission_id' => $permission->id,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('roles_and_permissions')->insert
        ([
            'role_id' => $guestRole->id,
            'permission_id' => Permission::where('name', 'get-list-user')->first()->id,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}