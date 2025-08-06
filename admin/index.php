<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /CARE/auth/login.php');
    exit;
}

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
    <div class="container my-5 d-flex flex-column justify-content-center align-items-center">
        <h2 class="text-center text-secondary fw-bold mb-4">Admin Dashboard</h2>

        <div class="row g-4">
            <div class="col-md-4">
                <a href="cities.php" class="text-decoration-none">
                    <div class="card shadow-sm text-center p-4 h-100">
                        <h5 class="text-primary">Manage Cities</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="doctors.php" class="text-decoration-none">
                    <div class="card shadow-sm text-center p-4 h-100">
                        <h5 class="text-success">Manage Doctors</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="patients.php" class="text-decoration-none">
                    <div class="card shadow-sm text-center p-4 h-100">
                        <h5 class="text-warning">Manage Patients</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="users.php" class="text-decoration-none">
                    <div class="card shadow-sm text-center p-4 h-100">
                        <h5 class="text-danger">Manage Users</h5>
                    </div>
                </a>
            </div>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>