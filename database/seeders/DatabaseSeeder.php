<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create the Super Admin group
        $superAdminGroup = UserGroup::updateOrCreate(
            ['id' => 1],
            [
                'name'         => 'Super Admin',
                'view'         => true,
                'control'      => true,
                'historical'   => true,
                'data_manager' => true,
            ]
        );

        // 2. Look for the existing admin user
        $adminUser = User::where('username', 'admin')->first();

        if (!$adminUser) {
            User::create([
                'username'      => 'admin',
                'first_name'    => 'Administrator', // <-- Changed from 'name' to 'first_name'
                'email'         => 'pmo.admin@rel.co.id',
                'password'      => Hash::make('RELadmin01!'),
                'user_group_id' => $superAdminGroup->id,
            ]);
        } else {
            $adminUser->update([
                'user_group_id' => $superAdminGroup->id,
            ]);
        }

        // 3. AUTOMATED POSTGRESQL FIX
        // Fast-forward the auto-increment counters so they don't collide with the forced IDs
        if (config('database.default') === 'pgsql') {
            DB::statement("SELECT setval('user_groups_id_seq', (SELECT MAX(id) FROM user_groups))");
            DB::statement("SELECT setval('users_id_seq', (SELECT MAX(id) FROM users))");
        }
    }
}