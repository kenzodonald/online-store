<?php
require_once 'header.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT p.*, c.name AS category FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { echo '<p>Product not found.</p>'; require_once 'footer.php'; exit; }
?>
<div class="product-detail">
  <div class="left">
    <?php if ($p['image']): ?><img src="<?php echo htmlspecialchars($p['image']); ?>" alt=""><?php else: ?><div class="placeholder">No image</div><?php endif; ?>
  </div>
  <div class="right">
    <h2><?php echo htmlspecialchars($p['name']); ?></h2>
    <p class="price">$<?php echo number_format($p['price'],2); ?></p>
    <p><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
    <form method="post" action="cart.php?action=add&id=<?php echo $p['id']; ?>">
      <label>Quantity: <input type="number" name="qty" value="1" min="1"></label>
      <button type="submit">Add to Cart</button>
    </form>
  </div>
</div>
<?php require_once 'footer.php'; ?>