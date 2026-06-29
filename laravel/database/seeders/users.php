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
                'name'     => 'Admin STI',
                'email'    => 'admin@sti.com',
                'role'     => 'admin',
                'password' => Hash::make('admin12345'),
            ],
            [
                'name'     => 'Finance STI',
                'email'    => 'finance@sti.com',
                'role'     => 'finance',
                'password' => Hash::make('finance12345'),
            ],
            [
                'name'     => 'Gudang STI',
                'email'    => 'gudang@sti.com',
                'role'     => 'gudang',
                'password' => Hash::make('gudang12345'),
            ],
            [
                'name'     => 'Direktur STI',
                'email'    => 'direktur@sti.com',
                'role'     => 'direktur',
                'password' => Hash::make('direktur12345'),
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