<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    echo "Booting kernel...\n";
    $app->boot();
    echo "Success!\n";
} catch (\Throwable $e) {
    echo "Caught: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
