<?php
require_once 'includes/config.php';
if (!empty($_SESSION['user'])) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $u = $stmt->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
        redirect('dashboard.php');
    }
    $error = 'Invalid email or password.';
}
$title = 'Login';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login · MediBook</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="auth-page">
<div class="auth-card">
  <div class="logo">💙</div>
  <h1>Welcome to MediBook</h1>
  <p class="muted">Book doctor appointments in seconds</p>
  <?php if(!empty($_SESSION['flash'])): [$m,$t]=$_SESSION['flash']; unset($_SESSION['flash']); ?><div class="alert <?= e($t) ?> flash"><?= e($m) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <label>Email</label><input type="email" name="email" placeholder="you@example.com" required>
    <label>Password</label>
    <div class="pw"><input type="password" name="password" placeholder="••••••" required><button type="button" class="eye" data-toggle-pw>👁</button></div>
    <button type="submit" class="btn-block">Login</button>
  </form>
  <div class="demo"><strong>Try a demo account</strong> (click to fill)
    <div class="chips">
      <span class="chip" data-fill="admin@example.com">👑 Admin</span>
      <span class="chip" data-fill="amit@example.com">🩺 Doctor</span>
      <span class="chip" data-fill="rahul@example.com">🙂 Patient</span>
    </div><small>Password: password</small></div>
  <p class="center">New patient? <a href="register.php">Create account</a></p>
</div>
<script src="assets/js/script.js"></script></body></html>
