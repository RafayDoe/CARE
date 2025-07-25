<?php
session_start();
include('../includes/db.php');

$registerError = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $name = trim($_POST['name']);
    $city = trim($_POST['city']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    $specialization = $_POST['specialization'] ?? null;
    $experience = $_POST['experience'] ?? null;

    if (!$username || !$email || !$role) {
        $registerError = "Please fill in all required fields.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $registerError = "Username or email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $password, $role);

            if ($stmt->execute()) {
                $user_id = $conn->insert_id;

                if ($role === 'doctor') {
                    $stmt2 = $conn->prepare("INSERT INTO doctors (user_id, name, specialization, experience) VALUES (?, ?, ?, ?)");
                    $stmt2->bind_param("issi", $user_id, $username, $specialization, $experience);
                    $stmt2->execute();
                }
                if ($role === 'patient') {
                    $stmt = $conn->prepare("INSERT INTO patients (user_id, name, email, phone, address, city) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssss", $user_id, $username, $email, $phone, $address, $city);
                    $stmt->execute();
                }





                $success = "Registered successfully! You can now log in.";
            } else {
                $registerError = "Something went wrong. Please try again.";
            }
        }
    }


}
?>

<?php include('../includes/header.php'); ?>

<div class="container my-5 d-flex justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="card shadow p-4 border-2" style="max-width: 600px; width: 100%;" data-aos="fade-up">
        <div class="card-body">
            <h2 class="text-center mb-4 fw-bold text-secondary">Register</h2>

            <?php if ($registerError): ?>
                <div class="alert alert-danger text-center"><?php echo $registerError; ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success text-center"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="" onsubmit="this.querySelector('button[type=submit]').disabled = true;">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>


                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" required>
                </div>


                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" id="roleSelect" required onchange="toggleDoctorFields()">
                        <option value="">Select Role</option>
                        <option value="patient">Patient</option>
                        <option value="doctor">Doctor</option>
                    </select>
                </div>

                <div id="doctorFields" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Specialization</label>
                        <input id="specialization" name="specialization" class="form-control"
                            data-list="Cardiologist, Dentist, Dermatologist, Endocrinologist, ENT Specialist, Gastroenterologist, General Physician, Gynecologist, Neurologist, Neurosurgeon, Oncologist, Orthopedic Surgeon, Pediatrician, Psychiatrist, Pulmonologist, Radiologist, Surgeon, Urologist"
                            autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Years of Experience</label>
                        <input type="number" name="experience" class="form-control" min="0">
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-secondary">Register</button>
                </div>

                <p class="text-center mt-3 text-muted">
                    Already have an account? <a href="/Project/auth/login.php" class="text-quaternary">Login</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleDoctorFields() {
        const role = document.getElementById('roleSelect').value;
        const container = document.getElementById('doctorFields');
        const inputs = container.querySelectorAll('input');

        if (role === 'doctor') {
            container.style.display = 'block';
            inputs.forEach(input => input.required = true);
        } else {
            container.style.display = 'none';
            inputs.forEach(input => input.required = false);
        }
    }

    document.addEventListener('DOMContentLoaded', toggleDoctorFields);
</script>

<?php include('../includes/footer.php'); ?>