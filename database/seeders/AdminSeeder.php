<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        Admin::create([
            'username' => 'MoevanStaff',
            'email' => 'worker@moevan.com',
            'password' => Hash::make('Moevan.Staff!@#$'),
        ]);
    }
}
