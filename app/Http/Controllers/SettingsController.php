<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Upload or replace the user's profile avatar.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate(['avatar' => 'required|image|max:3072']); // 3MB max

        /** @var User $user */
        $user = $request->user();

        // Delete old avatar if it exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return ResponseHelper::success([
            'avatar' => config('app.url') . '/storage/' . $path,
        ], 'Avatar updated successfully');
    }

    /**
     * List all active sessions for the user.
     */
    public function sessions(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $currentTokenId = $user->currentAccessToken()?->id;

        $sessions = $user->tokens()->orderBy('last_used_at', 'desc')->get()->map(function ($token) use ($currentTokenId) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
                'is_current' => $token->id === $currentTokenId,
            ];
        });

        return ResponseHelper::success($sessions, 'Active sessions retrieved');
    }

    /**
     * Revoke a specific session.
     */
    public function revokeSession(Request $request, $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $token = $user->tokens()->where('id', $id)->first();

        if (!$token) {
            return ResponseHelper::notFound('Session not found');
        }

        if ($token->id === $user->currentAccessToken()?->id) {
            return ResponseHelper::error('Cannot revoke current session. Please use logout instead.', null, 400);
        }

        $token->delete();

        return ResponseHelper::success(null, 'Session revoked successfully');
    }

    /**
     * Revoke all other sessions except the current one.
     */
    public function revokeOtherSessions(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $currentTokenId = $user->currentAccessToken()?->id;

        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        return ResponseHelper::success(null, 'All other sessions revoked successfully');
    }

    /**
     * Export user data as a JSON bundle.
     */
    public function exportData(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user()->load(['donations', 'volunteers']);

        $export = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
            'donations' => $user->donations->map(function ($donation) {
                return [
                    'amount' => $donation->amount,
                    'status' => $donation->status,
                    'payment_method' => $donation->payment_method,
                    'created_at' => $donation->created_at,
                ];
            }),
            'volunteer_records' => $user->volunteers->map(function ($volunteer) {
                return [
                    'status' => $volunteer->status,
                    'created_at' => $volunteer->created_at,
                ];
            }),
            'export_date' => now()->toIso8601String(),
        ];

        return ResponseHelper::success($export, 'User data exported');
    }
}
