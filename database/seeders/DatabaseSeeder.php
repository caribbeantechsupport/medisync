<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create one specific admin user
        User::create([
            'user_name' => 'adminuser',
            'first_name' => 'Admin',
            'middle_name' => null,
            'last_name' => 'User',
            'national_identification' => 'A123456789',
            'email' => 'admin@example.com',
            'contact_number' => '123-456-7890',
            'address' => '123 Admin St, Admin City',
            'country' => 'Adminland',
            'user_role' => 'admin',
            'password' => bcrypt('password'), // Make sure to set a strong password
        ]);
    }
}
