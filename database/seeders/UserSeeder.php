<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'super_admin',
            'email' => 'super_admin@project.com',
            'password' => bcrypt('00@11@22$33'),
        ]);
        $user->addRole('superadministrator');

        $blogger = User::create([
            'name' => 'blogger',
            'email' => 'blogger@project.com',
            'password' => bcrypt('00@11@22$33'),
        ]);
        $blogger->addRole('blogger');
    }
}
