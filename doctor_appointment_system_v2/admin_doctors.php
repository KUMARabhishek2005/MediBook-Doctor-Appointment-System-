<?php
require_once 'includes/config.php';
require_role('admin');
$title = 'Manage Doctors';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $pw = $_POST['password'] ?: 'password';
    $spec = (int)($_POST['specialization_id'] ?? 0); $qual = trim($_POST['qualification'] ?? '');
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $qual === '' || $spec <= 0) $error = 'Please fill all required fields.';
    else try {
        $pdo->beginTransaction(); // both inserts succeed or neither does
        $pdo->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,'doctor')")->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT)]);
        $uid = $pdo->lastInsertId();
        $pdo->prepare('INSERT INTO doctors(user_id,specialization_id,qualification,experience,fee) VALUES(?,?,?,?,?)')
            ->execute([$uid, $spec, $qual, (int)$_POST['experience'], (float)$_POST['fee']]);
        $pdo->commit(); flash("Dr. $name added."); redirect('admin_doctors.php');
    } catch (PDOException $e) { if ($pdo->inTransaction()) $pdo->rollBack(); $error = 'Could not add doctor. Email may already exist.'; }
}
$specs = $pdo->query('SELECT * FROM specializations ORDER BY name')->fetchAll();
$doctors = $pdo->query('SELECT d.*, u.name, u.email, s.name AS specialization FROM doctors d JOIN users u ON u.id=d.user_id JOIN specializations s ON s.id=d.specialization_id ORDER BY u.name')->fetchAll();
require 'includes/header.php';
?>
<h1>👨‍⚕️ Manage Doctors</h1>
<div class="card"><h2>➕ Add Doctor</h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><div class="form-grid">
  <div><label>Name</label><input name="name" required></div>
  <div><label>Email</label><input type="email" name="email" required></div>
  <div><label>Temporary password</label><input name="password" value="password"></div>
  <div><label>Specialization</label><select name="specialization_id" required><?php foreach ($specs as $s): ?><option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
  <div><label>Qualification</label><input name="qualification" required></div>
  <div><label>Experience (years)</label><input type="number" name="experience" min="0" value="0"></div>
  <div><label>Fee (₹)</label><input type="number" step="0.01" name="fee" min="0" value="500"></div>
</div><button>Add Doctor</button></form></div>
<div class="card"><h2>All Doctors</h2>
<input class="table-search" data-search="#tbl" placeholder="🔎 Filter doctors…">
<div class="table-wrap"><table id="tbl"><tr><th>Name</th><th>Specialization</th><th>Email</th><th>Exp.</th><th>Fee</th></tr>
<?php foreach ($doctors as $d): ?><tr><td><?= e($d['name']) ?></td><td><span class="tag"><?= e($d['specialization']) ?></span></td><td><?= e($d['email']) ?></td><td><?= (int)$d['experience'] ?> yrs</td><td>₹<?= number_format((float)$d['fee'],2) ?></td></tr><?php endforeach; ?></table></div></div>
<?php require 'includes/footer.php'; ?>
