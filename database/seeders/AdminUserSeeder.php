<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@devortega.com'],
            [
                'name' => 'Carlos Ortega',
                'password' => Hash::make(env('ADMIN_DEFAULT_PASSWORD', 'ChangeMe123!')),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
