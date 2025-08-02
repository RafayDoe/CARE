<?php
session_start();

include('../includes/db.php');
include('../includes/header.php');


if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-danger text-center'>Not logged in.</div>";
    exit;
}

$user_id = $_SESSION['user_id'];


$docStmt = $conn->prepare("SELECT id, name FROM doctors WHERE user_id = ?");
$docStmt->bind_param("i", $user_id);
$docStmt->execute();
$docResult = $docStmt->get_result();

if ($docResult->num_rows === 0) {
    echo "<div class='alert alert-danger text-center'>Doctor profile not found.</div>";
    exit;
}

$doctor = $docResult->fetch_assoc();
$doctor_id = $doctor['id'];
$doctor_name = $doctor['name'];


$apptStmt = $conn->prepare("
    SELECT a.*, p.name AS patient_name 
FROM appointments a
JOIN patients p ON a.patient_id = p.id
WHERE a.doctor_id = ?
ORDER BY a.appointment_time ASC
");
$apptStmt->bind_param("i", $doctor_id);
$apptStmt->execute();
$appointments = $apptStmt->get_result();
?>
<style>
    main {
        min-height: 90vh;
    }

    .container {
        text-align: center;
    }
</style>

<main>
    <div class="container my-5">
        <h2 class="mb-4">Welcome, <?= htmlspecialchars($doctor_name) ?></h2>
        <h4 class="mb-3">Your Appointments</h4>

        <?php if ($appointments->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Appointment Time</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appt = $appointments->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                                <td><?= htmlspecialchars($appt['appointment_time']) ?></td>
                                <td><?= htmlspecialchars($appt['status']) ?></td>
                                <td><?= htmlspecialchars($appt['created_at']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">You currently have no appointments.</div>
        <?php endif; ?>
    </div>
</main>

<?php include('../includes/footer.php'); ?>