<?php
require_once 'config.php';

// Handle Stripe success callback
if (isset($_GET['stripe_success']) && !empty($_GET['session_id'])) {
    $sessionId = $_GET['session_id'];
    // Verify payment with Stripe
    if (empty($STRIPE_SECRET)) { die('Stripe not configured. Set STRIPE_SECRET in config.php or env.'); }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/checkout/sessions/" . urlencode($sessionId) . "?expand[]=payment_intent");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, $STRIPE_SECRET . ":");
    $res = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($res, true);
    if (isset($data['payment_intent']) && isset($data['payment_intent']['status']) && $data['payment_intent']['status'] === 'succeeded') {
        // Create order from pending session stored in PHP session
        $pending = $_SESSION['pending_orders'][$sessionId] ?? null;
        if ($pending) {
            // add stripe_session column if missing
            $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS stripe_session VARCHAR(255) DEFAULT NULL");
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO orders (customer_name, customer_email, address, total, created_at, stripe_session) VALUES (?,?,?,?,NOW(),?)');
            $stmt->execute([$pending['name'],$pending['email'],$pending['address'],$pending['total'],$sessionId]);
            $orderId = $pdo->lastInsertId();
            $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)');
            foreach ($pending['cart'] as $pid => $q) {
                // fetch current price
                $pstmt = $pdo->prepare('SELECT price FROM products WHERE id = ?'); $pstmt->execute([$pid]); $prod = $pstmt->fetch();
                $price = $prod ? $prod['price'] : 0;
                $stmt->execute([$orderId, $pid, $q, $price]);
            }
            $pdo->commit();
            // clear pending and session cart
            unset($_SESSION['pending_orders'][$sessionId]);
            unset($_SESSION['cart']);
            require_once 'header.php';
            echo '<h2>Order placed</h2><p>Thank you — your order #' . htmlspecialchars($orderId) . ' has been received.</p>';
            require_once 'footer.php';
            exit;
        }
    }
    // If not succeeded
    header('Location: cart.php'); exit;
}

require_once 'header.php';
?>
<h2>Checkout</h2>
<form method="post" action="create_checkout_session.php">
  <label>Name: <input type="text" name="name" required></label><br>
  <label>Email: <input type="email" name="email"></label><br>
  <label>Address:<br><textarea name="address" rows="4" required></textarea></label><br>
  <button type="submit" class="btn">Proceed to Payment</button>
</form>
<?php require_once 'footer.php'; ?>