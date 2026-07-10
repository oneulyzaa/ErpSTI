<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'username' => 'superadmin',
            'password' => Hash::make('admin123'),
            'namalengkap' => 'SuperAdmin',
            'email' => 'admin@gmail.com',
            'akses' => 'superadmin',
        ]);
        
    }
}
