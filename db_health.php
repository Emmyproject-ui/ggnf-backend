<?php
// Standalone DB check
try {
    $pdo = new PDO('mysql:host=localhost;dbname=app_backend_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ SUCCESS: Database connected to app_backend_db\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    echo "User count: " . $stmt->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
