<?php
include('../includes/db.php');
include('../includes/auth.php');
require_role(['patient']);
include('../includes/header.php');
?>

<div class="text-center">
  <h1 class="mb-4">Welcome to CARE</h1>
  <p class="lead">Your trusted online platform for booking medical appointments and accessing health resources.</p>

  <div class="row mt-5">
    <div class="col-md-6">
      <a href="search-doctor.php" class="btn btn-primary btn-lg w-100">Search Doctors</a>
    </div>
    <div class="col-md-6 mt-3 mt-md-0">
      <a href="book-appointment.php" class="btn btn-success btn-lg w-100">Book Appointment</a>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
