<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/db.php");

$loginError = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                switch ($user['role']) {
                    case 'doctor':
                        header("Location: /CARE/doctor/index.php");
                        break;
                    case 'patient':
                        header("Location: /CARE/patient/index.php");
                        break;
                    case 'admin':
                        header("Location: /CARE/admin/index.php");
                        break;
                    default:
                        $loginError = "Unrecognized user role.";
                        break;
                }
                exit;
            } else {
                $loginError = "❌ Invalid password.";
            }
        } else {
            $loginError = "❌ User not found.";
        }
    } else {
        $loginError = "❌ Please fill in both fields.";
    }
}
?>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/header.php"); ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="card shadow p-4 border-2" style="max-width: 450px; width: 100%;" data-aos="fade-up">
        <div class="card-body">
            <h2 class="text-center mb-4 fw-bold text-secondary">Login</h2>

            <?php if ($loginError): ?>
                <div class="alert alert-danger text-center"><?= htmlspecialchars($loginError) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username or Email</label>
                    <input type="text" name="username" class="form-control" required autocomplete="username" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required autocomplete="current-password" />
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-secondary">Login</button>
                </div>
            </form>

            <p class="text-center mt-4 text-muted">
                Don't have an account?
                <a href="/CARE/auth/register.php" class="text-quaternary">Register</a>
            </p>
        </div>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/footer.php"); ?>
