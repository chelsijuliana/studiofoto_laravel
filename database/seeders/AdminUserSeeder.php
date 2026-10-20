<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama' => 'Admin Studio',
            'email' => 'admin@studio.com',
            'password' => Hash::make('password123'),
            'no_hp' => '081200000000',
            'role' => 'admin',
        ]);
    }
}