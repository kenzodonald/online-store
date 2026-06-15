<?php
session_start();
// Database configuration - adjust if your MySQL credentials differ
$DB_HOST = '127.0.0.1';
$DB_NAME = 'online_store';
$DB_USER = 'root';
$DB_PASS = '';
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Stripe configuration (set in local environment or here for testing)
$STRIPE_SECRET = getenv('STRIPE_SECRET') ?: '';
$STRIPE_PUBLISHABLE = getenv('STRIPE_PUBLISHABLE') ?: '';
?>