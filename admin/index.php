<?php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/../header.php';
?>
<div class="wrap admin-area">
  <h2>Admin Dashboard</h2>
  <p><a class="btn" href="products.php">Manage Products</a> <a href="logout.php">Logout</a></p>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>