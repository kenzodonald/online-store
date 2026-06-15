<?php
require_once 'header.php';
// show product list
$stmt = $pdo->query('SELECT p.*, c.name AS category FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC');
$products = $stmt->fetchAll();
?>
<div class="hero">
  <div class="left">
    <h2>Find quality products from around the world</h2>
    <p>Browse curated items across categories — electronics, books, clothing and more. Fast checkout, secure orders.</p>
    <p><a class="btn" href="#products">Shop Now</a></p>
  </div>
  <div class="image"><img src="assets/hero.svg" alt="Shopping"></div>
</div>

<h2 id="products" style="color:var(--surface);margin-bottom:12px">Products</h2>
<div class="product-grid">
<?php foreach ($products as $p): ?>
  <div class="product">
    <div class="image"><?php if ($p['image']): ?><img src="<?php echo htmlspecialchars($p['image']); ?>" alt=""><?php else: ?><div class="placeholder">No image</div><?php endif; ?></div>
    <h3><?php echo htmlspecialchars($p['name']); ?></h3>
    <p class="price">$<?php echo number_format($p['price'],2); ?></p>
    <p><a class="btn" href="product.php?id=<?php echo $p['id']; ?>">View</a></p>
  </div>
<?php endforeach; ?>
</div>
<?php require_once 'footer.php'; ?>