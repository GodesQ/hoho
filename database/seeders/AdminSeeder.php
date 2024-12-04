<?php

namespace Database\Seeders;

use App\Models\Admin;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'firstname' => "PEP",
            'lastname' => 'Test',
            'username' => 'peptest',
            'email' => 'peptest@gmail.com',
            'password' => Hash::make('Test123!'),
            'is_approved' => true,
            'role' => 'super_admin',
        ]);
    }
}
