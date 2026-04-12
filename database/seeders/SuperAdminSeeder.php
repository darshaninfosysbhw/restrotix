<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Master Admin',
            'email' => 'admin@restochain.com',
            'password' => bcrypt('password123'),
            'role' => 'superadmin',
            'is_active' => true,
            'tenant_id' => null,
            'branch_id' => null,
        ]);
    }
}
