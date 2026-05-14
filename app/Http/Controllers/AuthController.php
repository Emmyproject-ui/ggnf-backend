<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            $request->validated(),
            $request
        );

        return ResponseHelper::created([
            'user' => new UserResource($result['user']),
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'Registration successful');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->only(['email', 'password']),
            $request
        );

        if (!$result) {
            return ResponseHelper::unauthorized('Invalid login credentials');
        }

        return ResponseHelper::success([
            'user' => new UserResource($result['user']),
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return ResponseHelper::success(null, 'Logged out successfully');
    }

    public function me(Request $request): JsonResponse
    {
        return ResponseHelper::success(
            new UserResource($request->user()),
            'User profile retrieved'
        );
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'phone']));

        return ResponseHelper::success(
            new UserResource($user->fresh()),
            'Profile updated successfully'
        );
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return ResponseHelper::error('Current password is incorrect', null, 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return ResponseHelper::success(null, 'Password updated successfully');
    }
}
