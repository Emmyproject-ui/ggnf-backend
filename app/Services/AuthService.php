<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthService
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function register(array $data, Request $request): array
    {
        \Illuminate\Support\Facades\Log::info('Registering user: ' . $data['email']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'customer',
        ]);

        // Auto-verify email — no email verification flow in this app
        $user->markEmailAsVerified();

        \Illuminate\Support\Facades\Log::info('User created with ID: ' . $user->id);

        $token = $user->createToken('auth_token')->plainTextToken;

        \Illuminate\Support\Facades\Log::info('Token generated');

        $this->activityLogService->logAuth($user->id, 'signup', $request, [
            'email' => $user->email,
        ]);

        \Illuminate\Support\Facades\Log::info('Activity logged');

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials, Request $request): ?array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        // Email verification is auto-handled on registration — no block on login

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->activityLogService->logAuth($user->id, 'login', $request);

        $user->update(['last_login_at' => now()]);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }
    }
}
