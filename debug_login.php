<?php
require_once 'config/db.php';

$email = 'admin.dean@keris.edu.ph';
$testPassword = '123';

echo "<h2>Debug Report for $email</h2>";

// 1. Fetch user from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p style='color:red;'>❌ <strong>User not found in database!</strong> You need to insert this account into the 'users' table.</p>";
} else {
    echo "<p style='color:green;'>✅ <strong>User found in database!</strong></p>";
    echo "<pre>";
    print_r([
        'id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
        'password' => $user['password'] ?? 'COLUMN password DOES NOT EXIST!',
        'password_hash' => $user['password_hash'] ?? 'COLUMN password_hash DOES NOT EXIST!'
    ]);
    echo "</pre>";

    // 2. Check password verify
    if (isset($user['password_hash'])) {
        $verify = password_verify($testPassword, $user['password_hash']);
        if ($verify) {
            echo "<p style='color:green;'>✅ <strong>password_verify('123') MATCHES!</strong> Login logic should work.</p>";
        } else {
            echo "<p style='color:red;'>❌ <strong>password_verify('123') FAILED!</strong> The hash stored in the DB does not match '123'.</p>";
        }
    }
}