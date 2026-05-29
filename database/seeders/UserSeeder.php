<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Owner
        User::firstOrCreate(
            ['email' => 'owner@gmail.com'],
            [
                'name' => 'Owner Pebeel',
                'password' => Hash::make('password123'),
                'role' => 'owner',
            ]
        );

        // Manager
        User::firstOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'Manager Pebeel',
                'password' => Hash::make('password123'),
                'role' => 'manager',
            ]
        );

        // Kasir
        User::firstOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name' => 'Kasir Pebeel',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
            ]
        );

        // Kasir 3
        User::firstOrCreate(
            ['email' => 'kasir3@pebeel.local'],
            [
                'name' => 'Kasir Rina',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
            ]
        );
    }
}
