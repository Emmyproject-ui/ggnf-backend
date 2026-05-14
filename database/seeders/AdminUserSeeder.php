<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the admin user.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@ggnf.org'],
            [
                'name' => 'GGNF Admin',
                'password' => Hash::make('Ggnf@Admin2025!'),
                'role' => 'admin',
            ]
        );

        // Mark email as verified (not mass-assignable for security)
        if (!$admin->hasVerifiedEmail()) {
            $admin->markEmailAsVerified();
        }
    }
}
