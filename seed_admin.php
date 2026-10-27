<?php
require_once 'config/db.php';

$admins = [
    ['email' => 'admin.dean@ptc.edu.ph', 'full_name' => 'Dean Admin', 'role' => 'admin'],
    ['email' => 'admin.pcc@ptc.edu.ph', 'full_name' => 'IT & Esports Lead', 'role' => 'admin'],
    ['email' => 'admin.sports@ptc.edu.ph', 'full_name' => 'Sports Director', 'role' => 'admin'],
    ['email' => 'admin.council@ptc.edu.ph', 'full_name' => 'Student Council', 'role' => 'admin']
];

$passwordHash = password_hash('123', PASSWORD_BCRYPT);

foreach ($admins as $admin) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$admin['email']]);
    
    if ($stmt->fetch()) {
        $update = $pdo->prepare("UPDATE users SET password_hash = ?, role = ? WHERE email = ?");
        $update->execute([$passwordHash, $admin['role'], $admin['email']]);
        echo "Updated: " . htmlspecialchars($admin['email']) . "<br>";
    } else {
        $insert = $pdo->prepare("INSERT INTO users (email, password_hash, full_name, role) VALUES (?, ?, ?, ?)");
        $insert->execute([$admin['email'], $passwordHash, $admin['full_name'], $admin['role']]);
        echo "Inserted: " . htmlspecialchars($admin['email']) . "<br>";
    }
}
echo "<br><strong>Admin passwords updated to '123' successfully for @ptc.edu.ph!</strong>";
?>