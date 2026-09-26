<?php
require_once 'includes/config.php';
require_role('admin');
$title = 'All Appointments';
$rows = $pdo->query('SELECT a.*, p.name AS patient_name, du.name AS doctor_name, s.name AS specialization FROM appointments a JOIN users p ON p.id=a.patient_id JOIN doctors d ON d.id=a.doctor_id JOIN users du ON du.id=d.user_id JOIN specializations s ON s.id=d.specialization_id ORDER BY a.appointment_date DESC, a.appointment_time DESC')->fetchAll();
require 'includes/header.php';
?>
<h1>📅 All Appointments</h1>
<input class="table-search" data-search="#tbl" placeholder="🔎 Filter by patient, doctor, status…">
<div class="table-wrap"><table id="tbl"><tr><th>Patient</th><th>Doctor</th><th>Specialization</th><th>Date</th><th>Time</th><th>Status</th></tr>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['patient_name']) ?></td><td><?= e($r['doctor_name']) ?></td><td><?= e($r['specialization']) ?></td><td><?= e($r['appointment_date']) ?></td><td><?= e(substr($r['appointment_time'],0,5)) ?></td><td><?= status_badge($r['status']) ?></td></tr><?php endforeach; ?></table></div>
<?php if (!$rows): ?><div class="empty">📭 No appointments yet.</div><?php endif; ?>
<?php require 'includes/footer.php'; ?>
