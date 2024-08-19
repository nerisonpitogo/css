<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {



        // create permissions
        $permissions_to_add = ['Manage Users', 'Manage Settings'];
        foreach ($permissions_to_add as $permission) {
            Permission::create([
                'name' => $permission,
                'description' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // create roles
        $roles_to_add = ['Admin'];
        foreach ($roles_to_add as $role) {
            Role::create([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        // assign permissions to roles
        $role = Role::where('name', 'Admin')->first();
        $permissions = Permission::all();

        $role->permissions()->sync($permissions->pluck('id'));

        // assign user id 1 to admin
        $user = User::find(1);
        $user->assignRole($role);

        // insert services
        for ($i = 1; $i <= 25; $i++) {
            DB::table('lib_services')->insert([
                'service_name' => "Service $i",
                'service_description' => "Service $i description",
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
