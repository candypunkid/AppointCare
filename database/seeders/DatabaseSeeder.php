<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Create a default super admin if none exists
        if (! User::where('email', 'super@appointcare.test')->exists()) {
            User::factory()->create([
                'name' => 'Super Admin',
                'email' => 'super@appointcare.test',
                'role' => 'super_admin',
            ]);
        }
    }
}
