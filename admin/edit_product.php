<?php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
$prod = $pdo->prepare('SELECT * FROM products WHERE id = ?'); $prod->execute([$id]); $p = $prod->fetch();
$categories = $pdo->query('SELECT * FROM categories')->fetchAll();
if (!$p) { echo 'Product not found'; exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name']; $desc = $_POST['description']; $price = (float)$_POST['price']; $stock = (int)$_POST['stock']; $image = trim($_POST['image']); $category = $_POST['category'] ?: null;
    $pdo->prepare('UPDATE products SET name=?,description=?,price=?,image=?,stock=?,category_id=? WHERE id=?')->execute([$name,$desc,$price,$image,$stock,$category,$id]);
    header('Location: products.php'); exit;
}
require_once __DIR__ . '/../header.php';
?>
<div class="wrap admin-area">
  <h2>Edit Product</h2>
  <form method="post" style="max-width:720px">
    <label>Name: <input name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required></label><br>
    <label>Description:<br><textarea name="description"><?php echo htmlspecialchars($p['description']); ?></textarea></label><br>
    <label>Price: <input name="price" type="number" step="0.01" value="<?php echo $p['price']; ?>" required></label><br>
    <label>Stock: <input name="stock" type="number" value="<?php echo $p['stock']; ?>"></label><br>
    <label>Image URL: <input name="image" value="<?php echo htmlspecialchars($p['image']); ?>"></label><br>
    <label>Category: <select name="category"><option value="">-- none --</option><?php foreach ($categories as $c) echo '<option value="'.$c['id'].'"'.($p['category_id']==$c['id']?' selected':'').'>' . htmlspecialchars($c['name']) . '</option>'; ?></select></label><br>
    <button type="submit" class="btn">Save</button>
  </form>
  <p><a href="products.php">Back</a></p>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>