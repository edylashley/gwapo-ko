<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run()
    {
        // Create 25 test users to test pagination
        for ($i = 1; $i <= 25; $i++) {
            User::create([
                'name' => "Test User {$i}",
                'email' => "testuser{$i}@example.com",
                'password' => Hash::make('password123'),
                'is_admin' => $i <= 3 ? true : false, // First 3 are admins
                'email_verified_at' => now(),
            ]);
        }
    }
}