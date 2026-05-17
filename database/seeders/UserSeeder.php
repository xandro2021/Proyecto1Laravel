<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@officelend.com',
                'password' => Hash::make('admin'),
                'role' => 'ADMIN',
                'full_name' => 'Administrator'
            ]
        );

        // USER
        User::firstOrCreate(
            ['username' => 'user'],
            [
                'email' => 'user@officelend.com',
                'password' => Hash::make('user'),
                'role' => 'USER',
                'full_name' => 'Default User'
            ]
        );
    }
}
