<?php
require_once 'config.php';
action:
$action = isset($_GET['action']) ? $_GET['action'] : null;
if ($action === 'add') {
    $id = (int)($_GET['id'] ?? 0);
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (!isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] = 0;
    $_SESSION['cart'][$id] += $qty;
  // Persist to DB if user logged in
  if (!empty($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $stmt = $pdo->prepare('SELECT quantity FROM carts WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$uid, $id]);
    $row = $stmt->fetch();
    if ($row) {
      $newQ = (int)$row['quantity'] + $qty;
      $pdo->prepare('UPDATE carts SET quantity = ? WHERE user_id = ? AND product_id = ?')->execute([$newQ, $uid, $id]);
    } else {
      $pdo->prepare('INSERT INTO carts (user_id,product_id,quantity) VALUES (?,?,?)')->execute([$uid,$id,$qty]);
    }
  }
    header('Location: cart.php'); exit;
}
if ($action === 'update') {
  foreach ($_POST['qty'] as $id => $q) {
    $id = (int)$id; $q = max(0, (int)$q);
    if ($q === 0) {
      unset($_SESSION['cart'][$id]);
      if (!empty($_SESSION['user_id'])) $pdo->prepare('DELETE FROM carts WHERE user_id = ? AND product_id = ?')->execute([$_SESSION['user_id'],$id]);
    } else {
      $_SESSION['cart'][$id] = $q;
      if (!empty($_SESSION['user_id'])) $pdo->prepare('INSERT INTO carts (user_id,product_id,quantity) VALUES (?,?,?) ON DUPLICATE KEY UPDATE quantity = ?')->execute([$_SESSION['user_id'],$id,$q,$q]);
    }
  }
    header('Location: cart.php'); exit;
}
if ($action === 'remove') {
    $id = (int)($_GET['id'] ?? 0);
    unset($_SESSION['cart'][$id]);
  if (!empty($_SESSION['user_id'])) $pdo->prepare('DELETE FROM carts WHERE user_id = ? AND product_id = ?')->execute([$_SESSION['user_id'],$id]);
    header('Location: cart.php'); exit;
}
require_once 'header.php';
$cart = $_SESSION['cart'] ?? [];
$products = [];
$total = 0.0;
// If user is logged in and session cart is empty, load persisted cart
if (empty($cart) && !empty($_SESSION['user_id'])) {
  $stmt = $pdo->prepare('SELECT product_id,quantity FROM carts WHERE user_id = ?');
  $stmt->execute([$_SESSION['user_id']]);
  $rows = $stmt->fetchAll();
  foreach ($rows as $r) $cart[(int)$r['product_id']] = (int)$r['quantity'];
  $_SESSION['cart'] = $cart;
}
if ($cart) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($cart));
    $products = $stmt->fetchAll(PDO::FETCH_UNIQUE);
}
?>
<h2>Your Cart</h2>
<?php if (!$cart): ?>
  <p>Your cart is empty.</p>
<?php else: ?>
  <form method="post" action="cart.php?action=update">
  <table class="cart">
    <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr>
    <?php foreach ($cart as $pid => $qty):
        $p = $products[$pid];
        $subtotal = $p['price'] * $qty;
        $total += $subtotal;
    ?>
      <tr>
        <td><?php echo htmlspecialchars($p['name']); ?></td>
        <td>$<?php echo number_format($p['price'],2); ?></td>
        <td><input type="number" name="qty[<?php echo $pid; ?>]" value="<?php echo $qty; ?>" min="0"></td>
        <td>$<?php echo number_format($subtotal,2); ?></td>
        <td><a href="cart.php?action=remove&id=<?php echo $pid; ?>">Remove</a></td>
      </tr>
    <?php endforeach; ?>
    <tr><td colspan="3">Total</td><td>$<?php echo number_format($total,2); ?></td><td></td></tr>
  </table>
  <button type="submit">Update Cart</button>
  </form>
  <p><a href="checkout.php">Proceed to Checkout</a></p>
<?php endif; ?>
<?php require_once 'footer.php'; ?>