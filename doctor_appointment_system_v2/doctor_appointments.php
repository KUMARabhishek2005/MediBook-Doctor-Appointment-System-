<?php
require_once 'includes/config.php';
require_role('doctor');
$title = 'Appointments';
$stmt = $pdo->prepare('SELECT id FROM doctors WHERE user_id=?'); $stmt->execute([$_SESSION['user']['id']]);
$doctorId = $stmt->fetchColumn();
if (!$doctorId) exit('Doctor profile not found.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    if (in_array($status, ['Approved','Rejected','Completed'], true)) {
        $pdo->prepare('UPDATE appointments SET status=? WHERE id=? AND doctor_id=?')->execute([$status, (int)$_POST['id'], $doctorId]);
        flash("Appointment marked as $status.");
    }
    redirect('doctor_appointments.php');
}
$stmt = $pdo->prepare('SELECT a.*, u.name AS patient_name, u.email FROM appointments a JOIN users u ON u.id=a.patient_id WHERE a.doctor_id=? ORDER BY a.appointment_date DESC, a.appointment_time DESC');
$stmt->execute([$doctorId]); $rows = $stmt->fetchAll();
require 'includes/header.php';
?>
<h1>📋 Patient Appointments</h1>
<input class="table-search" data-search="#tbl" placeholder="🔎 Filter by patient, status…">
<div class="table-wrap"><table id="tbl"><tr><th>Patient</th><th>Email</th><th>Date</th><th>Time</th><th>Reason</th><th>Status</th><th>Update</th></tr>
<?php foreach ($rows as $r): ?><tr>
  <td><?= e($r['patient_name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['appointment_date']) ?></td>
  <td><?= e(substr($r['appointment_time'],0,5)) ?></td><td><?= e($r['reason']) ?></td><td><?= status_badge($r['status']) ?></td>
  <td><form method="post" class="inline"><input type="hidden" name="id" value="<?= $r['id'] ?>">
    <button name="status" value="Approved" class="ok small">✔</button>
    <button name="status" value="Rejected" class="danger small">✖</button>
    <button name="status" value="Completed" class="small">🏁</button></form></td>
</tr><?php endforeach; ?></table></div>
<?php if (!$rows): ?><div class="empty">📭 No appointments yet.</div><?php endif; ?>
<?php require 'includes/footer.php'; ?>
