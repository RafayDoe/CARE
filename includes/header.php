<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CARE - Home</title>
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
    rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/Project/css/styles.css?v=<?= time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.min.js"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="70" tabindex="0">

  <nav class="navbar navbar-expand-lg sticky-top shadow-sm"
    style="background-color: var(--secondary); border-bottom: 1px solid rgba(0,0,0,0.05);">
    <div class="container-fluid px-3">
      <a class="navbar-brand fw-bold text-white fs-4" href="/Project/index.php#hero">
        CARE
      </a>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        $isAuthPage = in_array($currentPage, ['login.php', 'register.php']);
        ?>

        <ul class="navbar-nav ms-auto align-items-center">
          <?php if (!$isAuthPage): ?>
            <li class="nav-item"><a class="nav-link text-white px-3" href="/Project/index.php#hero">Home</a></li>
            <li class="nav-item"><a class="nav-link text-white px-3" href="#doctors">Doctors</a></li>
            <li class="nav-item"><a class="nav-link text-white px-3" href="#about">About</a></li>
          <?php endif; ?>

          <?php if (!isset($_SESSION['role'])): ?>
            <?php if (!$isAuthPage): ?>
              <li class="nav-item"><a class="nav-link text-white px-3" href="#patient-controls">Patients</a></li>
              <li class="nav-item"><a class="nav-link text-white px-3" href="#doctor-cta">Join Us</a></li>
            <?php endif; ?>
            <li class="nav-item ms-2">
              <a href="/Project/auth/login.php" class="btn btn-light btn-sm px-3">Login</a>
            </li>
          <?php else: ?>
            <li class="nav-item ms-2">
              <?php if ($_SESSION['role'] === 'patient'): ?>
                <a href="/Project/patient/index.php" class="nav-link text-white px-3">Dashboard</a>
              <?php elseif ($_SESSION['role'] === 'doctor'): ?>
                <a href="/Project/doctor/index.php" class="nav-link text-white px-3">Dashboard</a>
              <?php elseif ($_SESSION['role'] === 'admin'): ?>
                <a href="/Project/admin/index.php" class="nav-link text-white px-3">Admin Panel</a>
              <?php endif; ?>
            </li>

            <li class="nav-item ms-2">
              <a href="/Project/auth/logout.php" class="btn btn-light btn-sm px-3">Logout</a>
            </li>
          <?php endif; ?>
        </ul>


      </div>
    </div>
  </nav>