<?php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['is_admin'])) { header('Location: login.php'); exit; }
$categories = $pdo->query('SELECT * FROM categories')->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $image = trim($_POST['image']);
    $category = $_POST['category'] ?: null;
    $stmt = $pdo->prepare('INSERT INTO products (name,description,price,image,stock,category_id) VALUES (?,?,?,?,?,?)');
    $stmt->execute([$name,$desc,$price,$image,$stock,$category]);
    header('Location: products.php'); exit;
}
require_once __DIR__ . '/../header.php';
?>
<div class="wrap admin-area">
  <h2>Add Product</h2>
  <form method="post" style="max-width:720px">
    <label>Name: <input name="name" required></label><br>
    <label>Description:<br><textarea name="description"></textarea></label><br>
    <label>Price: <input name="price" type="number" step="0.01" required></label><br>
    <label>Stock: <input name="stock" type="number" value="0"></label><br>
    <label>Image URL: <input name="image"></label><br>
    <label>Category: <select name="category"><option value="">-- none --</option><?php foreach ($categories as $c) echo '<option value="'.$c['id'].'">'.htmlspecialchars($c['name']).'</option>'; ?></select></label><br>
    <button type="submit" class="btn">Add</button>
  </form>
  <p><a href="products.php">Back</a></p>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>