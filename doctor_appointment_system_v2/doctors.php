<?php
require_once 'includes/config.php';
require_role('patient');
$title = 'Find a Doctor';
$specializations = $pdo->query('SELECT * FROM specializations ORDER BY name')->fetchAll();
$q = trim($_GET['q'] ?? ''); $spec = (int)($_GET['specialization'] ?? 0);

$sql = "SELECT d.*, u.name, s.name AS specialization FROM doctors d
        JOIN users u ON u.id=d.user_id JOIN specializations s ON s.id=d.specialization_id WHERE 1=1";
$params = [];
if ($q !== '') { $sql .= ' AND (u.name LIKE ? OR s.name LIKE ?)'; array_push($params, "%$q%", "%$q%"); }
if ($spec)     { $sql .= ' AND d.specialization_id=?'; $params[] = $spec; }
$stmt = $pdo->prepare($sql . ' ORDER BY u.name'); $stmt->execute($params); $doctors = $stmt->fetchAll();
require 'includes/header.php';
?>
<h1>🩺 Find a Doctor</h1>
<form class="filter" method="get">
  <input name="q" placeholder="Search doctor or specialization…" value="<?= e($q) ?>">
  <select name="specialization"><option value="0">All specializations</option>
    <?php foreach ($specializations as $s): ?><option value="<?= $s['id'] ?>" <?= $spec === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?>
  </select><button>Search</button>
</form>
<div class="grid">
<?php foreach ($doctors as $d): ?>
  <div class="card doc pop grad-<?= $d['specialization_id'] % 5 ?>">
    <div class="avatar"><?= e(initials($d['name'])) ?></div>
    <h2><?= e($d['name']) ?></h2>
    <span class="tag"><?= e($d['specialization']) ?></span>
    <p class="muted"><?= e($d['qualification']) ?> · <?= (int)$d['experience'] ?> yrs experience</p>
    <p class="fee">₹<?= number_format((float)$d['fee'], 2) ?></p>
    <a class="button" href="book_appointment.php?doctor=<?= $d['id'] ?>">Book now →</a>
  </div>
<?php endforeach; ?>
</div>
<?php if (!$doctors): ?><div class="empty">😕 No doctors found. Try another search.</div><?php endif; ?>
<?php require 'includes/footer.php'; ?>
