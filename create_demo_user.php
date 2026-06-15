<?php
require_once 'config.php';
// Creates a demo user with username 'demo' and password 'demo123'
$username = 'demo';
$password = 'demo123';
$email = 'demo@example.com';
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo "User '{$username}' already exists.<br>";
    echo "You can login at <a href=\"login.php\">login.php</a>.";
    exit;
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (username,password_hash,email) VALUES (?,?,?)');
$stmt->execute([$username, $hash, $email]);
echo "Created user '{$username}' with password '{$password}'.<br>";
echo "Login at <a href=\"login.php\">login.php</a>.";
?>
