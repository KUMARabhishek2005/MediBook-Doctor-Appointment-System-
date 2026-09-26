<?php
require_once 'includes/config.php';
require_login();
$title = 'Dashboard';
$role = $_SESSION['user']['role']; $uid = $_SESSION['user']['id'];
$stats = []; // label => [value, icon, colour class]

if ($role === 'admin') {
    $stats = [
      'Doctors'      => [$pdo->query('SELECT COUNT(*) FROM doctors')->fetchColumn(), '👨‍⚕️', 'c-blue'],
      'Patients'     => [$pdo->query("SELECT COUNT(*) FROM users WHERE role='patient'")->fetchColumn(), '🙂', 'c-green'],
      'Appointments' => [$pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn(), '📅', 'c-orange'],
      'Pending'      => [$pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Pending'")->fetchColumn(), '⏳', 'c-pink'],
    ];
} else {
    $where = $role === 'doctor' ? 'doctor_id=(SELECT id FROM doctors WHERE user_id=?)' : 'patient_id=?';
    $count = function (string $extra = '') use ($pdo, $where, $uid) {
        $s = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE $where $extra"); $s->execute([$uid]); return $s->fetchColumn();
    };
    $stats = [
      'Total'     => [$count(), '📅', 'c-blue'],
      'Pending'   => [$count("AND status='Pending'"), '⏳', 'c-orange'],
      'Approved'  => [$count("AND status='Approved'"), '✅', 'c-green'],
      'Completed' => [$count("AND status='Completed'"), '🏁', 'c-purple'],
    ];
}
require 'includes/header.php';
?>
<div class="hero">
  <div><h1>Hello, <?= e($_SESSION['user']['name']) ?> 👋</h1><p>Here is your <?= e($role) ?> overview for today.</p></div>
  <?php if ($role === 'patient'): ?><a class="button light" href="doctors.php">➕ Book Appointment</a><?php endif; ?>
</div>
<div class="cards">
<?php foreach ($stats as $label => [$val, $icon, $cls]): ?>
  <div class="card stat <?= $cls ?>"><div class="icon"><?= $icon ?></div>
    <div><div class="big" data-count="<?= (int)$val ?>">0</div><div class="lbl"><?= e($label) ?></div></div></div>
<?php endforeach; ?>
</div>
<?php require 'includes/footer.php'; ?>
