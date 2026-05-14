<?php

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Admin Backend Verification Script ---\n";

// 1. Create Test Users
echo "\n1. Creating/Resetting Test Users...\n";
User::where('email', 'admin@test.com')->forceDelete();
User::where('email', 'user@test.com')->forceDelete();

$admin = User::create([
    'name' => 'Test Admin',
    'email' => 'admin@test.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);
$adminToken = $admin->createToken('test-admin')->plainTextToken;
echo "Admin created: {$admin->email} (Token: " . substr($adminToken, 0, 10) . "...)\n";

$user = User::create([
    'name' => 'Test User',
    'email' => 'user@test.com',
    'password' => Hash::make('password'),
    'role' => 'user',
]);
$userToken = $user->createToken('test-user')->plainTextToken;
echo "User created: {$user->email} (Token: " . substr($userToken, 0, 10) . "...)\n";

// 2. Simulate User Activity (Login)
echo "\n2. Simulating User Login (to generate log)...\n";
// Manually creating log since we can't easily curl localhost from here without a server running, 
// but we want to test the *retrieval* logic mostly. 
// However, let's try to simulate the Controller logic directly if possible, or just insert data.
// For robust testing, we'll insert data directly as if the controller did it.
ActivityLog::create([
    'user_id' => $user->id,
    'type' => 'login',
    'description' => "Test User logged in",
    'ip_address' => '127.0.0.1',
    'user_agent' => 'TestScript/1.0',
]);
echo "Simulated login log created.\n";

// 3. Simulate Donation
echo "\n3. Simulating Donation...\n";
$user->donations()->create([
    'amount' => 5000,
    'payment_reference' => 'TEST-DON-' . time(),
    'status' => 'completed',
]);
ActivityLog::create([
    'user_id' => $user->id,
    'type' => 'donation',
    'description' => "Donation of ₦5,000 received",
    'metadata' => ['amount' => 5000, 'reference' => 'TEST-REF'],
    'ip_address' => '127.0.0.1',
]);
echo "Simulated donation and log created.\n";

// 4. Test Admin Access (Internal Request Simulation)
echo "\n4. Testing Admin Access to Activity Logs...\n";
$request = Illuminate\Http\Request::create('/api/admin/activity-logs', 'GET');
$request->setUserResolver(function () use ($admin) {
    return $admin;
});

$controller = app(App\Http\Controllers\AdminController::class);
$response = $controller->getActivityLogs($request); // Changed from activityLogs()
$content = $response->getContent();
$data = json_decode($content, true);

// Laravel Paginated Resource returns { success: true, message: ..., data: { data: [...], links: ..., meta: ... } }
$logs = $data['data']['data'] ?? $data['data'] ?? [];

if (count($logs) > 0) {
    echo "SUCCESS: Retrieved " . count($logs) . " activity logs.\n";
    $log = reset($logs);
    echo "Log Keys: " . implode(', ', array_keys($log)) . "\n";
    if (isset($log['description'])) {
        echo "Sample Log: " . $log['description'] . "\n";
    } else {
        echo "Sample Log: " . json_encode($log) . "\n";
    }
} else {
    echo "FAILURE: No activity logs retrieved.\n";
    print_r($data);
}

// 5. Test Admin Access to Users
echo "\n5. Testing Admin Access to Users...\n";
$response = $controller->getUsers($request); // Changed from users()
$data = json_decode($response->getContent(), true);
$usersList = $data['data']['data'] ?? $data['data'] ?? [];
if (count($usersList) >= 2) {
    echo "SUCCESS: Retrieved " . count($usersList) . " users.\n";
} else {
    echo "FAILURE: User retrieval count mismatch.\n";
    print_r($data);
}

// 6. Test Access Denial (Manual Check needed for Middleware)
echo "\n6. Middleware Logic Check...\n";
$middleware = new App\Http\Middleware\AdminMiddleware();
$request = Illuminate\Http\Request::create('/api/admin/users', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user; // Non-admin user
});

try {
    $response = $middleware->handle($request, function () {
        return response('Allowed');
    });
    if ($response->getStatusCode() === 403) {
        echo "SUCCESS: Non-admin user blocked (403 Forbidden).\n";
    } else {
        echo "FAILURE: Non-admin user was NOT blocked. Status: " . $response->getStatusCode() . "\n";
    }
} catch (\Exception $e) {
    echo "Error checking middleware: " . $e->getMessage() . "\n";
}

echo "\n--- Verification Complete ---\n";
