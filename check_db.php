<?php
$host = '127.0.0.1';
$port = '3306';
$username = 'root';
$password = '';
$database = 'app_backend_db';

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "MySQL connection successful.\n";
    
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
    if ($stmt->fetch()) {
        echo "Database '$database' exists.\n";
    } else {
        echo "Database '$database' does not exist. Attempting to create...\n";
        $pdo->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "Database '$database' created successfully.\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
