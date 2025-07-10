<?php
session_start();
include('../includes/db.php');

$loginError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'patient'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        echo "</pre>";

        $password = trim($_POST['password']);

        if (password_verify($password, $user['password'])) {
            // ✅ Login success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("refresh:2;url=index.php"); 
            exit();
        } else {
            $loginError = "❌ Invalid password.";
        }
    } else {
        $loginError = "❌ User not found or not a patient.";
    }
}
?>

<?php include('../includes/header.php'); ?>

<div class="card shadow mx-auto" style="max-width: 500px; background: white;">
    <div class="card-body">
        <h2 class="text-center text-primary mb-4">Patient Login</h2>

        <?php if ($loginError): ?>
            <div class="alert alert-danger text-center"><?php echo $loginError; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label>Email or Username</label>
                <input type="text" name="username" class="form-control" autocomplete="username" required />
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" autocomplete="current-password" required />
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>