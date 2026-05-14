<?php

/**
 * Authentication Flow Verification Test
 *
 * Tests all critical auth paths after the authentication layer reconstruction.
 * Run: php test_auth_flow.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

$passed = 0;
$failed = 0;

function test(string $name, callable $fn): void
{
    global $passed, $failed;
    try {
        $result = $fn();
        if ($result === true) {
            echo "  ✅ PASS: {$name}\n";
            $passed++;
        } else {
            echo "  ❌ FAIL: {$name} — returned: " . json_encode($result) . "\n";
            $failed++;
        }
    } catch (\Throwable $e) {
        echo "  ❌ FAIL: {$name} — Exception: {$e->getMessage()}\n";
        $failed++;
    }
}

echo "\n=== AUTHENTICATION FLOW VERIFICATION ===\n\n";

// --- Phase 1: Database State ---
echo "--- Database State ---\n";

test('Admin user exists with verified email', function () {
    $admin = User::where('email', 'admin@ggnf.org')->first();
    return $admin && $admin->role === 'admin' && $admin->hasVerifiedEmail();
});

test('Customer user exists with verified email', function () {
    $customer = User::where('email', 'customer@example.com')->first();
    return $customer && $customer->role === 'customer' && $customer->hasVerifiedEmail();
});

test('Admin password is correctly hashed', function () {
    $admin = User::where('email', 'admin@ggnf.org')->first();
    return $admin && Hash::check('Ggnf@Admin2025!', $admin->password);
});

test('Customer password is correctly hashed', function () {
    $customer = User::where('email', 'customer@example.com')->first();
    return $customer && Hash::check('Customer123!', $customer->password);
});

// --- Phase 2: AuthService Logic ---
echo "\n--- AuthService Login ---\n";

$authService = $app->make(\App\Services\AuthService::class);

test('Admin login returns token', function () use ($authService) {
    $request = Request::create('/api/login', 'POST');
    $result = $authService->login([
        'email' => 'admin@ggnf.org',
        'password' => 'Ggnf@Admin2025!',
    ], $request);
    return $result !== null
        && isset($result['user'], $result['token'])
        && $result['user']->role === 'admin'
        && strlen($result['token']) > 10;
});

test('Customer login returns token', function () use ($authService) {
    $request = Request::create('/api/login', 'POST');
    $result = $authService->login([
        'email' => 'customer@example.com',
        'password' => 'Customer123!',
    ], $request);
    return $result !== null
        && isset($result['user'], $result['token'])
        && $result['user']->role === 'customer'
        && strlen($result['token']) > 10;
});

test('Both users can have active tokens simultaneously', function () use ($authService) {
    $request = Request::create('/api/login', 'POST');

    $adminResult = $authService->login([
        'email' => 'admin@ggnf.org',
        'password' => 'Ggnf@Admin2025!',
    ], $request);

    $customerResult = $authService->login([
        'email' => 'customer@example.com',
        'password' => 'Customer123!',
    ], $request);

    // Both should have valid tokens
    return $adminResult !== null
        && $customerResult !== null
        && $adminResult['token'] !== $customerResult['token'];
});

test('Wrong password returns null (not exception)', function () use ($authService) {
    $request = Request::create('/api/login', 'POST');
    $result = $authService->login([
        'email' => 'admin@ggnf.org',
        'password' => 'WrongPassword!',
    ], $request);
    return $result === null;
});

test('Non-existent email returns null (not exception)', function () use ($authService) {
    $request = Request::create('/api/login', 'POST');
    $result = $authService->login([
        'email' => 'nonexistent@example.com',
        'password' => 'SomePassword123!',
    ], $request);
    return $result === null;
});

// --- Phase 3: Config Verification ---
echo "\n--- Configuration ---\n";

test('Sanctum guard is empty (pure token auth)', function () {
    $guard = config('sanctum.guard');
    return is_array($guard) && count($guard) === 0;
});

test('Sanctum expiration is set', function () {
    return config('sanctum.expiration') !== null;
});

test('Auth default guard is web (standard)', function () {
    return config('auth.defaults.guard') === 'web';
});

test('CORS supports_credentials is true', function () {
    return config('cors.supports_credentials') === true;
});

// --- Summary ---
echo "\n=== RESULTS ===\n";
echo "  Passed: {$passed}\n";
echo "  Failed: {$failed}\n";
$total = $passed + $failed;
echo "  Total: {$total}\n";
echo $failed === 0 ? "\n  🎉 ALL TESTS PASSED\n\n" : "\n  ⚠️  SOME TESTS FAILED\n\n";

exit($failed > 0 ? 1 : 0);
