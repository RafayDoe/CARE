<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: /CARE/auth/login.php");
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/db.php");
include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/header.php");

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctorId = $_POST['doctor_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $userId = $_SESSION['user_id'] ?? null;

    $getPatient = $conn->prepare("SELECT id FROM patients WHERE user_id = ?");
    $getPatient->bind_param("i", $userId);
    $getPatient->execute();
    $patientResult = $getPatient->get_result();
    $patientRow = $patientResult->fetch_assoc();
    $getPatient->close();

    $patientId = $patientRow['id'] ?? null;

    if ($doctorId && $date && $time && $patientId) {
        $appointmentTime = date('Y-m-d H:i:s', strtotime("$date $time"));

        $stmt = $conn->prepare("
            INSERT INTO appointments (patient_id, doctor_id, appointment_time, status, created_at)
            VALUES (?, ?, ?, 'pending', NOW())
        ");
        $stmt->bind_param("iis", $patientId, $doctorId, $appointmentTime);

        if ($stmt->execute()) {
            $successMsg = "Appointment booked successfully!";
        } else {
            $errorMsg = "Failed to book appointment. Try again.";
        }

        $stmt->close();
    } else {
        $errorMsg = "Please complete all fields.";
    }
}
$doctors = [];
$result = $conn->query("SELECT id, name, specialization, experience, image FROM doctors");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}
?>

<div class="container py-5">
  <div class="card p-4 shadow-sm">
    <h2 class="mb-4">Book an Appointment</h2>

    <?php if ($successMsg): ?>
      <div class="alert alert-success"><?= $successMsg ?></div>
    <?php elseif ($errorMsg): ?>
      <div class="alert alert-danger"><?= $errorMsg ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="doctor_id" class="form-label">Choose Doctor</label>
        <select name="doctor_id" id="doctor_id" class="form-select" required>
          <option value="">-- Select --</option>
          <?php foreach ($doctors as $doc): ?>
            <option value="<?= $doc['id'] ?>">
              <?= htmlspecialchars($doc['name']) ?> (<?= htmlspecialchars($doc['specialization']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="date" class="form-label">Date</label>
        <input type="date" name="date" id="date" class="form-control" min="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="mb-3">
        <label for="time" class="form-label">Time</label>
        <input type="time" name="time" id="time" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-tertiary text-white">Book Now</button>
    </form>
  </div>

  <div class="mt-5">
    <h4 class="mb-4">Available Doctors</h4>
    <div class="row" id="doctors">
      <?php foreach ($doctors as $doc): ?>
        <div class="col-md-4 mb-3 d-flex justify-content-center">
          <div class="card text-center p-2">
            <?php if (!empty($doc['image'])): ?>
              <img src="/CARE/assets/<?= htmlspecialchars($doc['image']) ?>" class="card-img-top" alt="Doctor Image">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($doc['name']) ?></h5>
              <p class="card-text">
                <?= htmlspecialchars($doc['specialization']) ?><br>
                <?= htmlspecialchars($doc['experience']) ?> years experience
              </p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/footer.php"); ?>
