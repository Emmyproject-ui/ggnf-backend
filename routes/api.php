<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\ProjectController;
use App\Helpers\ResponseHelper;

// Test endpoint for verifying API connection
Route::get('/test', function () {
    return ResponseHelper::success(['status' => 'ok'], 'API is running');
});

Route::get('/test-db', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        return ResponseHelper::success(null, 'Database connection established');
    } catch (\Exception $e) {
        return ResponseHelper::serverError('Database connection failed');
    }
});

Route::get('/ping', function () {
    return ResponseHelper::success(['timestamp' => now()->toIso8601String()], 'pong');
});

// Public Blog Routes
Route::get('/blog', [BlogPostController::class, 'index']);
Route::get('/blog/{slug}', [BlogPostController::class, 'show']);

// Public Project Routes
Route::get('/projects', [ProjectController::class, 'index']);

// Auth Routes with rate limiting
Route::middleware(['throttle:auth'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/signup', [AuthController::class, 'register']); // Alias
    Route::post('/login', [AuthController::class, 'login']);
});

// Contact route with rate limiting
Route::middleware(['throttle:contact'])->group(function () {
    Route::post('/contact', [ContactController::class, 'store']);
});

// Authenticated Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/password', [AuthController::class, 'updatePassword']);

    // Customer Routes
    Route::middleware([\App\Http\Middleware\CustomerMiddleware::class])->group(function () {
        Route::post('/donate', [DonationController::class, 'store']);
        Route::get('/my-donations', [DonationController::class, 'userHistory']);

        Route::post('/volunteer', [VolunteerController::class, 'store']);
        Route::post('/join-us', [VolunteerController::class, 'store']); // Alias
        Route::post('/joinus', [VolunteerController::class, 'store']); // Alias
        Route::get('/my-volunteering', [VolunteerController::class, 'history']);

        // Settings
        Route::get('/settings/sessions', [\App\Http\Controllers\SettingsController::class, 'sessions']);
        Route::delete('/settings/sessions/{id}', [\App\Http\Controllers\SettingsController::class, 'revokeSession']);
        Route::delete('/settings/sessions', [\App\Http\Controllers\SettingsController::class, 'revokeOtherSessions']);
        Route::get('/settings/export', [\App\Http\Controllers\SettingsController::class, 'exportData']);
        Route::post('/settings/avatar', [\App\Http\Controllers\SettingsController::class, 'uploadAvatar']);
    });

    // Admin Routes
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/donations', [AdminController::class, 'getDonations']);
        Route::get('/payments', [AdminController::class, 'payments']);
        Route::get('/contributions', [AdminController::class, 'contributions']);

        Route::get('/volunteers', [AdminController::class, 'getVolunteers']);
        Route::patch('/volunteers/{id}/approve', [AdminController::class, 'approveVolunteer']);
        Route::patch('/volunteers/{id}/reject', [AdminController::class, 'rejectVolunteer']);

        Route::get('/messages', [AdminController::class, 'messages']);

        Route::get('/stats', [AdminController::class, 'stats']);
        Route::get('/activity-logs', [AdminController::class, 'getActivityLogs']);
        Route::get('/login-logs', [AdminController::class, 'loginLogs']); // Alias
        Route::get('/security-logs', [AdminController::class, 'securityLogs']);

        // Blog Posts (admin)
        Route::get('/blog', [BlogPostController::class, 'adminIndex']);
        Route::post('/blog', [BlogPostController::class, 'store']);
        Route::post('/blog/{id}', [BlogPostController::class, 'update']); // POST for form-data
        Route::delete('/blog/{id}', [BlogPostController::class, 'destroy']);

        // Projects (admin)
        Route::get('/projects', [ProjectController::class, 'adminIndex']);
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::post('/projects/{id}', [ProjectController::class, 'update']);
        Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);

        // Image upload
        Route::post('/upload', [BlogPostController::class, 'upload']);
    });
});
