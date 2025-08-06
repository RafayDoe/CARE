<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/db.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/auth.php");
require_role(['patient']);
include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/header.php");

$userId = $_SESSION['user_id'] ?? null;
?>

<style>
  .dashboard-bg {
    background-image: url('/CARE/assets/bgcurve.png');
    background-size: contain;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
  }

  .glass-card {
    background: rgba(255, 255, 255, 0.88);
    border-radius: 16px;
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }

  .glass-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
  }
</style>

<section class="dashboard-bg py-5">
  <div class="container">
    <div class="text-center mb-5 text-white" data-aos="fade-down">
      <h1 class="fw-bold display-5">Welcome Back</h1>
      <p class="lead">Your health tools are just a click away.</p>
    </div>

    <div class="row justify-content-center g-4">
      <div class="col-md-4" data-aos="zoom-in">
        <div class="card glass-card text-center border-0 h-100 p-4">
          <i class="bi bi-calendar-plus display-4 text-tertiary mb-3"></i>
          <h5 class="fw-bold mb-2">Book Appointment</h5>
          <p class="text-muted">Easily schedule visits with available doctors.</p>
          <a href="/CARE/patient/book-appointment.php" class="btn btn-tertiary mt-3">Book Now</a>
        </div>
      </div>

      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="150">
        <div class="card glass-card text-center border-0 h-100 p-4">
          <i class="bi bi-clock-history display-4 text-tertiary mb-3"></i>
          <h5 class="fw-bold mb-2">Your Appointments</h5>
          <p class="text-muted">Track upcoming or past visits and timings.</p>
          <a href="/CARE/patient/appointments.php" class="btn btn-tertiary mt-3">View</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
$patientStmt = $conn->prepare("SELECT id FROM patients WHERE user_id = ?");
$patientStmt->bind_param("i", $userId);
$patientStmt->execute();
$patientResult = $patientStmt->get_result();
$patient = $patientResult->fetch_assoc();
$patientStmt->close();

$patientId = $patient['id'] ?? null;
$upcomingStmt = $conn->prepare("
    SELECT a.appointment_time, d.name AS doctor_name
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ? AND a.appointment_time > NOW()
    ORDER BY a.appointment_time ASC
    LIMIT 1
");
$upcomingStmt->bind_param("i", $patientId);
$upcomingStmt->execute();
$upcomingResult = $upcomingStmt->get_result();
?>

<div class="mt-5" data-aos="fade-up">
  <?php if ($upcomingResult->num_rows > 0):
    $row = $upcomingResult->fetch_assoc(); ?>
    <div class="alert alert-info shadow-sm text-center" style="max-width: 600px; margin: 0 auto;">
      <strong>Upcoming Appointment:</strong>
      with <span class="text-primary"><?= htmlspecialchars($row['doctor_name']) ?></span> on
      <span class="fw-bold"><?= date("F j, Y - g:i A", strtotime($row['appointment_time'])) ?></span>
    </div>
  <?php else: ?>
    <div class="alert alert-light border text-muted text-center" style="max-width: 600px; margin: 0 auto;">
      No upcoming appointments. <a href="/CARE/patient/book-appointment.php">Book one now</a>.
    </div>
  <?php endif; ?>
</div>

<div class="mt-5 text-center" data-aos="fade-in">
  <div class="card border-0 shadow-sm p-4 mx-auto" style="max-width: 600px;">
    <h5 class="fw-bold mb-3">💡 Health Tip of the Day</h5>
    <p class="text-muted mb-0">
      Stay hydrated. Drinking enough water helps regulate your body temperature, improve digestion, and boost energy.
    </p>
  </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/footer.php"); ?>
