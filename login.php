<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
  $stmt->execute([$username]);
  $user = $stmt->fetch();
  if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    // Merge DB cart into session cart
    $cartRows = $pdo->prepare('SELECT product_id,quantity FROM carts WHERE user_id = ?');
    $cartRows->execute([$user['id']]);
    $dbCart = $cartRows->fetchAll();
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    foreach ($dbCart as $r) {
      $pid = (int)$r['product_id']; $q = (int)$r['quantity'];
      if (!isset($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] = 0;
      $_SESSION['cart'][$pid] += $q;
    }
    header('Location: index.php'); exit;
  } else {
    $error = 'Invalid credentials';
  }
}
require_once 'header.php';
?>
<h2>Login</h2>
<?php if (!empty($error)) echo '<p class="error">' . htmlspecialchars($error) . '</p>'; ?>
<form method="post" action="login.php">
  <label>Username: <input name="username" required></label><br>
  <label>Password: <input name="password" type="password" required></label><br>
  <button type="submit">Login</button>
</form>
<p>Don't have an account? <a href="register.php">Register</a></p>
<?php require_once 'footer.php'; ?>