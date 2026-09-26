<?php
// Shared top of every logged-in page. Set $title before including.
require_once __DIR__ . '/config.php';
$role = $_SESSION['user']['role'] ?? '';
$links = [
  'patient' => [['dashboard.php','🏠','Dashboard'],['doctors.php','🩺','Find Doctor'],['my_appointments.php','📅','My Appointments']],
  'doctor'  => [['dashboard.php','🏠','Dashboard'],['doctor_appointments.php','📋','Appointments']],
  'admin'   => [['dashboard.php','🏠','Dashboard'],['admin_doctors.php','👨‍⚕️','Doctors'],['admin_appointments.php','📅','Appointments']],
][$role] ?? [];
$current = basename($_SERVER['SCRIPT_NAME']);
?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title ?? 'MediBook') ?> · MediBook</title>
<link rel="stylesheet" href="assets/css/style.css">
</head><body>
<header class="topbar">
  <a class="brand" href="dashboard.php">💙 MediBook</a>
  <button class="burger" aria-label="Menu">☰</button>
  <nav>
    <?php foreach ($links as [$href,$icon,$label]): ?>
      <a href="<?= $href ?>" class="<?= $current === $href ? 'active' : '' ?>"><?= $icon ?> <?= $label ?></a>
    <?php endforeach; ?>
    <span class="user-chip"><?= e(initials($_SESSION['user']['name'])) ?> · <?= e(ucfirst($role)) ?></span>
    <a href="logout.php" class="logout">Logout</a>
  </nav>
</header>
<main class="container">
<?php if (!empty($_SESSION['flash'])): [$m,$t] = $_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert <?= e($t) ?> flash"><?= e($m) ?></div>
<?php endif; ?>
