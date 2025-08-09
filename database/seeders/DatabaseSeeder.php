<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a default user with a known password
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => Hash::make('admin123'), // bcrypt hash
            'is_admin' => true, // Set admin flag
        ]);
    }
}
