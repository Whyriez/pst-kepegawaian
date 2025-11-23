<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kemenag.go.id',
            'password' => Hash::make('password'), // password default
            'role' => 'admin'
        ]);

        // 2. Akun Pegawai (Sesuai desain profil sebelumnya)
        User::create([
            'name' => 'User Pegawai',
            'email' => 'user.pegawai@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);
    }
}
