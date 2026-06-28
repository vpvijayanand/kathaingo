<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@kathaingo.com'],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('Admin@1234'),
                'is_admin' => true,
                'is_approved' => true,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // If user already existed, ensure they have admin role
        if (!$user->wasRecentlyCreated) {
            $user->update([
                'is_admin' => true,
                'is_approved' => true,
                'role' => 'admin',
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        }
    }
}
