<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class users extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $users = [
            [
                'username' => 'admin',
                'password' => Hash::make('admin12345'),
                'namalengkap' => 'Admin STI',
                'email' => 'admin@sti.com',
                'akses' => 'admin',
            ],
            [
                'username' => 'finance',
                'password' => Hash::make('finance12345'),
                'namalengkap' => 'Finance STI',
                'email' => 'finance@sti.com',
                'akses' => 'finance',
            ],
            [
                'username' => 'gudang',
                'password' => Hash::make('gudang12345'),
                'namalengkap' => 'Gudang STI',
                'email' => 'gudang@sti.com',
                'akses' => 'gudang',
            ],
            [
                'username' => 'direktur',
                'password' => Hash::make('direktur12345'),
                'namalengkap' => 'Direktur STI',
                'email' => 'direktur@sti.com',
                'akses' => 'direktur',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}