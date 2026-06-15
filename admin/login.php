<?php
require_once __DIR__ . '/../config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if ($user === 'admin' && $pass === 'admin123') {
        $_SESSION['is_admin'] = true;
        header('Location: products.php'); exit;
    } else {
        $error = 'Invalid credentials';
    }
}
require_once __DIR__ . '/../header.php';
?>
<div class="wrap admin-area">
  <h2>Admin Login</h2>
  <?php if (!empty($error)) echo '<p class="error">' . htmlspecialchars($error) . '</p>'; ?>
  <form method="post" style="max-width:420px">
    <label>Username: <input name="username" required></label><br>
    <label>Password: <input name="password" type="password" required></label><br>
    <button type="submit" class="btn">Login</button>
  </form>
  <p><a href="/online-store/index.php">Back to store</a></p>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>