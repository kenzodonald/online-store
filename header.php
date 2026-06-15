<?php
require_once __DIR__ . '/config.php';
function cart_count() {
    if (!isset($_SESSION['cart'])) return 0;
    $count = 0; foreach ($_SESSION['cart'] as $qty) $count += $qty;
    return $count;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Online Store</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <meta name="theme-color" content="#0f172a">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
  <div class="wrap">
    <h1><a href="/online-store/index.php">Online Store</a></h1>
    <nav>
      <a href="index.php">Products</a>
      <a href="cart.php">Cart (<?php echo cart_count(); ?>)</a>
      <?php if (!empty($_SESSION['username'])): ?>
        <a href="profile.php">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
      <a href="admin/login.php">Admin</a>
    </nav>
  </div>
</header>
<main class="wrap">
