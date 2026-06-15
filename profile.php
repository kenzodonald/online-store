<?php
require_once 'config.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$stmt = $pdo->prepare('SELECT id,username,email,created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
require_once 'header.php';
?>
<h2>Your Profile</h2>
<p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
<p><strong>Member since:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
<p><a href="index.php">Back to store</a></p>
<?php require_once 'footer.php'; ?>
