<?php
require_once 'includes/config.php';
if (!empty($_SESSION['user'])) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $pw = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pw) < 6) {
        $error = 'Enter a valid name, email and a password of at least 6 characters.';
    } else {
        try {
            $pdo->prepare('INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)')
                ->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT), 'patient']);
            flash('Account created! Please log in.'); redirect('login.php');
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000' ? 'Email already registered.' : 'Registration failed.';
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register · MediBook</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="auth-page"><div class="auth-card">
  <div class="logo">📝</div><h1>Create patient account</h1>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <label>Full name</label><input name="name" required>
    <label>Email</label><input type="email" name="email" required>
    <label>Password (min 6)</label>
    <div class="pw"><input type="password" name="password" minlength="6" required><button type="button" class="eye" data-toggle-pw>👁</button></div>
    <button class="btn-block">Register</button>
  </form>
  <p class="center"><a href="login.php">← Back to login</a></p>
</div><script src="assets/js/script.js"></script></body></html>
