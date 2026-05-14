<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerUserSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('Customer123!'),
                'role' => 'customer',
            ]
        );

        // Mark email as verified (not mass-assignable for security)
        if (!$customer->hasVerifiedEmail()) {
            $customer->markEmailAsVerified();
        }
    }
}
