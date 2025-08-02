<?php include("./includes/header.php");
?>

<section id="hero">
    <div class="container-fluid cover d-flex flex-column justify-content-center">
        <div class="row">
            <div class="col-md-7 ps-5">
                <h1 class="display-3 fw-bold text-white" data-aos="fade-right">
                    Your Health. Our Priority.
                </h1>
                <p class="h2 lead text-white mt-3 mb-4" data-aos="fade-up" data-aos-delay="200">
                    Empowering lives through accessible care.
                </p>
                <div data-aos="fade-up" data-aos-delay="400">
                    <a href="/auth/register.php">
                        <button class="btn btn-tertiary me-1">Register</button>
                    </a>
                    <a href="/auth/login.php">
                        <button class="btn btn-quaternary">Login</button>
                    </a>
                </div>
            </div>
            <div class="col-md-5"></div>
        </div>
    </div>
</section>

<section id="doctors" class="py-5">
  <div class="container py-5">
    <h2 class="text-center fw-bold mb-5">Find Doctors</h2>

    <?php
    include('./includes/db.php');

    $query = "
      SELECT d.name, d.specialization, d.experience, d.image
      FROM doctors d
      JOIN users u ON d.user_id = u.id
      WHERE u.role = 'doctor'
      ORDER BY d.id DESC
      LIMIT 6
    ";

    $result = $conn->query($query);
    ?>

    <?php if ($result && $result->num_rows > 0): ?>
      <div id="doctorCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">

          <?php
          $i = 0;
          while ($row = $result->fetch_assoc()):
            if ($i % 3 == 0): ?>
              <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                <div class="row justify-content-center g-4">
            <?php endif; ?>

            <div class="col-md-4 d-flex justify-content-center">
              <div class="card shadow-sm h-100">
                <img src="/assets/<?= htmlspecialchars($row['image']) ?>" class="card-img-top"
                  alt="<?= htmlspecialchars($row['name']) ?>">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                  <p class="card-text text-muted">
                    <?= htmlspecialchars($row['specialization']) ?><br>
                    <?= htmlspecialchars($row['experience']) ?> years experience
                  </p>
                  <a href="#" class="btn btn-primary w-100">View Profile</a>
                </div>
              </div>
            </div>

            <?php if ($i % 3 == 2 || $i == $result->num_rows - 1): ?>
                </div>
              </div>
            <?php endif;
            $i++;
          endwhile;
          ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#doctorCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#doctorCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>

    <?php else: ?>
      <p class="text-center">No doctors found yet.</p>
    <?php endif; ?>
  </div>
</section>


<section id="about" class="py-5">
    <div class="container px-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-up">
                <img src="/assets/aboutimg.png" alt="About CARE" class="img-fluid rounded" />
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <h2 class="fw-bold mb-4">About CARE</h2>
                <p class="lead text-muted">
                    CARE is a modern, people-first healthcare platform built to make quality medical services accessible
                    to everyone, anywhere.
                </p>
                <ul class="list-unstyled mt-4">
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-tertiary me-2"></i> Easy appointment booking
                    </li>
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-tertiary me-2"></i> Secure prescriptions and
                        history</li>
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-tertiary me-2"></i> Personalized experience
                    </li>
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-tertiary me-2"></i> Efficient and modern
                    </li>
                </ul>
                <a href="#patient-controls" class="btn btn-tertiary mt-3">Get Started</a>
            </div>
        </div>
    </div>
</section>

<section id="patient-controls" class="py-5 px-5">
    <div class="container d-flex justify-content-center flex-column align-items-center">
        <h2 class="text-center fw-bold mb-4" data-aos="fade-up">For Patients</h2>
        <div class="row justify-content-center g-5">
            <div class="col-md-4" data-aos="fade-up">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <i class="bi bi-calendar-check display-4 text-tertiary mb-3"></i>
                            <h5 class="card-title my-2">Book Appointments</h5>
                            <p class="card-text">Easily search and schedule appointments with verified doctors.</p>
                        </div>
                        <a href="/auth/login.php" class="btn btn-tertiary w-100 mt-4">Book Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <i class="bi bi-file-medical display-4 text-tertiary mb-3"></i>
                            <h5 class="card-title my-2">View Prescriptions</h5>
                            <p class="card-text">Access your prescription history and treatment plans anytime.</p>
                        </div>
                        <a href="/auth/login.php" class="btn btn-tertiary w-100 mt-4">Log In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="doctor-cta" class="py-5 bg-light px-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 ps-4" data-aos="fade-right">
                <h2 class="fw-bold mb-3">Are You a Doctor?</h2>
                <p class="lead text-muted mb-4">
                    Join the CARE platform and become part of a digital revolution in healthcare. Expand your reach,
                    manage your patients efficiently, and provide care on your own schedule — all while making a real
                    difference.
                </p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-quaternary me-2"></i> Wider patient reach
                    </li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-quaternary me-2"></i> Flexible online
                        schedule</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-quaternary me-2"></i> Secure and reliable
                        platform</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-quaternary me-2"></i> Free profile creation
                    </li>
                </ul>
                <a href="/doctor/register.php" class="btn btn-quaternary btn-lg mt-3">Join as Doctor</a>
            </div>
            <div class="col-lg-6 text-center" data-aos="fade-left">
                <img src="/assets/cta.png" alt="Join as Doctor" class="img-fluid rounded shadow-sm"
                    style="max-height: 350px;" />
            </div>
        </div>
    </div>
</section>

<?php include("./includes/footer.php") ?>