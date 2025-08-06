<?php include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/header.php"); ?>

<div class="text-center py-5" style="min-height: 80vh;">
    <h1 class="text-danger fw-bold mb-3">Unauthorized Access</h1>
    <p class="lead text-muted">You need to log in as a patient to access this page.</p>
    <a href="/CARE/auth/login.php" class="btn btn-secondary mt-4">Go to Login</a>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/footer.php"); ?>
