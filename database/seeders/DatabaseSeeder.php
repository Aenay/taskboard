<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            DemoUsersSeeder::class,
        ]);

        // Assign roles to users
        $user = \App\Models\User::find(1);
        $user->assignRole('admin');

        $user2 = \App\Models\User::find(2);
        $user2->assignRole('project-manager');

        $user3 = \App\Models\User::find(3);
        $user3->assignRole('member');
    }
}
