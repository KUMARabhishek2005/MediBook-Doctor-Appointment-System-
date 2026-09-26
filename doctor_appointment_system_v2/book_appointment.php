<?php
require_once 'includes/config.php';
require_role('patient');
$title = 'Book Appointment';
$doctorId = (int)($_GET['doctor'] ?? $_POST['doctor'] ?? 0);
$stmt = $pdo->prepare('SELECT d.*, u.name, s.name AS specialization FROM doctors d JOIN users u ON u.id=d.user_id JOIN specializations s ON s.id=d.specialization_id WHERE d.id=?');
$stmt->execute([$doctorId]); $doctor = $stmt->fetch();
if (!$doctor) exit('Doctor not found.');

$today = date('Y-m-d');
$date = $_GET['date'] ?? $_POST['date'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $time = $_POST['time'] ?? ''; $reason = trim($_POST['reason'] ?? '');
    if ($date < $today || !preg_match('/^\d\d:\d\d$/', $time)) $error = 'Please choose a valid date and time slot.';
    else try {
        $pdo->prepare('INSERT INTO appointments(patient_id,doctor_id,appointment_date,appointment_time,reason) VALUES(?,?,?,?,?)')
            ->execute([$_SESSION['user']['id'], $doctorId, $date, $time, $reason]);
        flash('Appointment requested! Waiting for the doctor to approve.'); redirect('my_appointments.php');
    } catch (PDOException $e) {
        $error = $e->getCode() === '23000' ? 'Sorry, that slot was just booked. Pick another.' : 'Could not book appointment.';
    }
}
// Build 30-minute slots 09:00–16:30 and mark the ones already taken.
$booked = [];
if ($date >= $today && $date !== '') {
    $s = $pdo->prepare("SELECT TIME_FORMAT(appointment_time,'%H:%i') FROM appointments WHERE doctor_id=? AND appointment_date=? AND status NOT IN ('Rejected','Cancelled')");
    $s->execute([$doctorId, $date]); $booked = $s->fetchAll(PDO::FETCH_COLUMN);
}
$slots = []; for ($m = 9*60; $m < 17*60; $m += 30) $slots[] = sprintf('%02d:%02d', intdiv($m,60), $m%60);
require 'includes/header.php';
?>
<div class="narrow">
<a href="doctors.php">← Back to doctors</a>
<div class="card book">
  <div class="doc-head"><div class="avatar"><?= e(initials($doctor['name'])) ?></div>
    <div><h2><?= e($doctor['name']) ?></h2><span class="tag"><?= e($doctor['specialization']) ?></span> <strong>₹<?= number_format((float)$doctor['fee'],2) ?></strong></div></div>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="doctor" value="<?= $doctorId ?>">
    <label>1. Pick a date</label>
    <input type="date" name="date" min="<?= $today ?>" value="<?= e($date) ?>" data-reload-date required>
    <?php if ($date >= $today && $date !== ''): ?>
      <label>2. Pick a time</label>
      <div class="slots">
        <?php foreach ($slots as $t): $taken = in_array($t, $booked, true); ?>
          <label class="slot <?= $taken ? 'taken' : '' ?>"><input type="radio" name="time" value="<?= $t ?>" <?= $taken ? 'disabled' : 'required' ?>><span><?= $t ?></span></label>
        <?php endforeach; ?>
      </div>
      <label>3. Reason (optional)</label><textarea name="reason" maxlength="255" placeholder="e.g. fever since 2 days"></textarea>
      <button class="btn-block">Confirm Appointment ✅</button>
    <?php else: ?><p class="muted">Choose a date to see available time slots.</p><?php endif; ?>
  </form>
</div></div>
<?php require 'includes/footer.php'; ?>
