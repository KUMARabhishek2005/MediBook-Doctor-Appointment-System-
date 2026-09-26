<?php
require_once 'includes/config.php';
require_role('patient');
$title = 'My Appointments';
// Cancel is a POST (safer than a link that changes data).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    $pdo->prepare("UPDATE appointments SET status='Cancelled' WHERE id=? AND patient_id=? AND status='Pending'")
        ->execute([(int)$_POST['cancel'], $_SESSION['user']['id']]);
    flash('Appointment cancelled.', 'info'); redirect('my_appointments.php');
}
$stmt = $pdo->prepare('SELECT a.*, u.name AS doctor_name, s.name AS specialization FROM appointments a JOIN doctors d ON d.id=a.doctor_id JOIN users u ON u.id=d.user_id JOIN specializations s ON s.id=d.specialization_id WHERE a.patient_id=? ORDER BY a.appointment_date DESC, a.appointment_time DESC');
$stmt->execute([$_SESSION['user']['id']]); $rows = $stmt->fetchAll();
require 'includes/header.php';
?>
<h1>📅 My Appointments</h1>
<input class="table-search" data-search="#tbl" placeholder="🔎 Filter appointments…">
<div class="table-wrap"><table id="tbl"><tr><th>Doctor</th><th>Specialization</th><th>Date</th><th>Time</th><th>Status</th><th></th></tr>
<?php foreach ($rows as $r): ?><tr>
  <td><?= e($r['doctor_name']) ?></td><td><?= e($r['specialization']) ?></td><td><?= e($r['appointment_date']) ?></td>
  <td><?= e(substr($r['appointment_time'],0,5)) ?></td><td><?= status_badge($r['status']) ?></td>
  <td><?php if ($r['status'] === 'Pending'): ?><form method="post" data-confirm="Cancel this appointment?"><input type="hidden" name="cancel" value="<?= $r['id'] ?>"><button class="danger small">Cancel</button></form><?php endif; ?></td>
</tr><?php endforeach; ?></table></div>
<?php if (!$rows): ?><div class="empty">📭 No appointments yet. <a href="doctors.php">Book your first one</a>.</div><?php endif; ?>
<?php require 'includes/footer.php'; ?>
