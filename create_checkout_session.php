<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: cart.php'); exit; }
if (empty($STRIPE_SECRET)) die('Stripe not configured. Set STRIPE_SECRET in config.php or env.');
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { header('Location: cart.php'); exit; }
// Load products and compute total
$placeholders = implode(',', array_fill(0, count($cart), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute(array_keys($cart));
$products = $stmt->fetchAll(PDO::FETCH_UNIQUE);
$total = 0; foreach ($cart as $pid => $q) $total += $products[$pid]['price'] * $q;
// Prepare Stripe Checkout session via cURL
$fields = [];
$idx = 0;
foreach ($cart as $pid => $q) {
    $p = $products[$pid];
    $priceCents = (int)round($p['price'] * 100);
    $fields["line_items[$idx][price_data][currency]"] = 'usd';
    $fields["line_items[$idx][price_data][product_data][name]"] = $p['name'];
    $fields["line_items[$idx][price_data][unit_amount]"] = $priceCents;
    $fields["line_items[$idx][quantity]"] = $q;
    $idx++;
}
$fields['mode'] = 'payment';
$base = rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']), "/\\");
$fields['success_url'] = $base . '/checkout.php?stripe_success=1&session_id={CHECKOUT_SESSION_ID}';
$fields['cancel_url'] = $base . '/cart.php';
// add metadata for reference
$fields['metadata[name]'] = $name;
$fields['metadata[email]'] = $email;
$fields['metadata[address]'] = $address;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, $STRIPE_SECRET . ':');
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
$res = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$data = json_decode($res, true);
if ($httpcode !== 200 && $httpcode !== 201) {
    die('Stripe API error: ' . ($data['error']['message'] ?? $res));
}
$sessionId = $data['id'] ?? null;
$sessionUrl = $data['url'] ?? null;
if (!$sessionId || !$sessionUrl) { die('Invalid Stripe response.'); }
// Store pending order in session keyed by session id
if (!isset($_SESSION['pending_orders'])) $_SESSION['pending_orders'] = [];
$_SESSION['pending_orders'][$sessionId] = [
    'name'=>$name,
    'email'=>$email,
    'address'=>$address,
    'cart'=>$cart,
    'total'=>$total
];
// Redirect to Stripe Checkout
header('Location: ' . $sessionUrl);
exit;
?>
