<?php
session_start();
include('../includes/db.php');


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$patient_id = $_SESSION['user_id'];
$message = "";

if (isset($_GET['cancel'])) {
    $cancel_id = intval($_GET['cancel']);
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ? AND patient_id = ?");
    $stmt->bind_param("ii", $cancel_id, $patient_id);
    $stmt->execute();
    $message = "Appointment cancelled successfully.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reschedule_id'])) {
    $reschedule_id = intval($_POST['reschedule_id']);
    $new_time = $_POST['new_time'];

    $stmt = $conn->prepare("UPDATE appointments SET appointment_time = ? WHERE id = ? AND patient_id = ?");
    $stmt->bind_param("sii", $new_time, $reschedule_id, $patient_id);
    $stmt->execute();
    $message = "Appointment rescheduled successfully.";
}
$stmt = $conn->prepare("
    SELECT a.id, a.appointment_time, a.status, d.name AS doctor_name, d.specialization 
    FROM appointments a
    LEFT JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_time DESC
");


$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

include('../includes/header.php');
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
        <h2 class="mb-4 fw-bold text-secondary">My Appointments</h2>
        <a href="./book-appointment.php" class="btn btn-primary mb-4">Book New Appointment</a>


        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                                <td><?php echo date("Y-m-d H:i", strtotime($row['appointment_time'])); ?></td>
                                <td><?php echo ucfirst($row['status']); ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="reschedule_id" value="<?php echo $row['id']; ?>">
                                        <input type="datetime-local" name="new_time" required
                                            class="form-control form-control-sm d-inline-block w-auto mb-2">
                                        <button class="btn btn-sm btn-warning" type="submit">Reschedule</button>
                                    </form>
                                    <a href="?cancel=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Cancel this appointment?')">Cancel</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No appointments found.</p>
        <?php endif; ?>
    </div>
</main>

<?php include('../includes/footer.php'); ?>