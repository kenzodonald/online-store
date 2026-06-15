<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username,password_hash,email) VALUES (?,?,?)');
            $stmt->execute([$username, $hash, $email]);
            header('Location: login.php'); exit;
        }
    }
}
require_once 'header.php';
?>
<h2>Register</h2>
<?php if (!empty($error)) echo '<p class="error">' . htmlspecialchars($error) . '</p>'; ?>
<form method="post" action="register.php">
  <label>Username: <input name="username" required></label><br>
  <label>Email: <input name="email" type="email"></label><br>
  <label>Password: <input name="password" type="password" required></label><br>
  <button type="submit">Create account</button>
</form>
<?php require_once 'footer.php'; ?>
