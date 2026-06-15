<?php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    header('Location: products.php'); exit;
}
$products = $pdo->query('SELECT p.*, c.name AS category FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC')->fetchAll();
require_once __DIR__ . '/../header.php';
?>
<div class="wrap admin-area">
  <h2>Products - Admin</h2>
  <p><a class="btn" href="add_product.php">Add Product</a> <a href="../index.php">View Store</a> <a href="logout.php">Logout</a></p>
  <div style="overflow:auto">
  <table class="admin-table">
    <tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Category</th><th>Actions</th></tr>
    <?php foreach ($products as $p): ?>
    <tr>
      <td><?php echo $p['id']; ?></td>
      <td><?php echo htmlspecialchars($p['name']); ?></td>
      <td>$<?php echo number_format($p['price'],2); ?></td>
      <td><?php echo $p['stock']; ?></td>
      <td><?php echo htmlspecialchars($p['category']); ?></td>
      <td><a href="edit_product.php?id=<?php echo $p['id']; ?>">Edit</a> | <a class="danger" href="products.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete?')">Delete</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
  </div>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>