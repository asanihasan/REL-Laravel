<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserGroup; // Make sure to import this!
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create the Super Admin group first so the ID exists
        $superAdminGroup = UserGroup::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Super Admin',
                'control' => true,
                'engine' => true,
                'pump' => true,
                'historical' => true,
                'data_manager' => true,
                'user_management' => true,
            ]
        );

        // 2. Look for the existing admin user
        $adminUser = User::where('username', 'admin')->first();

        if (!$adminUser) {
            // 3a. If they don't exist, create them with the new group ID
            User::create([
                'username' => 'admin',
                'password' => Hash::make('RELadmin01!'),
                'user_group_id' => $superAdminGroup->id,
            ]);
        } else {
            // 3b. If they already exist, just update their group ID 
            // (This protects their password in case they changed it!)
            $adminUser->update([
                'user_group_id' => $superAdminGroup->id,
            ]);
        }
    }
}