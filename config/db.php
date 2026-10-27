<?php
// Database Configuration
// For Localhost (XAMPP): keep values as 'localhost', 'root', ''
// For Hostinger (Production): update with your hPanel MySQL credentials
$host = 'localhost';
$dbname = 'ptc_event_hub';
$username = 'root'; // Change to your Hostinger DB username on production
$password = '';     // Change to your Hostinger DB password on production

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>