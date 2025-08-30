<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un administrateur
        User::create([
            'id' => Str::uuid(),
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '0123456789',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // Créer un utilisateur standard
        User::create([
            'id' => Str::uuid(),
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'phone' => '0987654321',
            'role' => 'user',
            'password' => Hash::make('user123'),
        ]);
    }
}
