<?php
include("./includes/header.php")
    ?>

<section id="hero">
    <div class="container-fluid cover d-flex flex-column justify-content-center">
        <div class="row">
            <div class="col-md-7 ps-5">
                <h1 class="display-3 fw-bold text-white">
                    Your Health. Our Priority.
                </h1>
                <p class="h2 lead text-white mt-3 mb-4">
                    Empowering lives through accessible care.
                </p>
                <a href="/Project/doctor/register.php">
                    <button class="btn btn-tertiary me-1">Register</button>
                </a>
                <a href="/Project/patient/login.php">
                    <button class="btn btn-quaternary">Login</button>
                </a>
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

        $query = "SELECT * FROM doctors LIMIT 6"; 
        $result = $conn->query($query);

        if ($result->num_rows > 0):
            ?>
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
                                        <img src="/Project/assets/<?= htmlspecialchars($row['image']) ?>" class="card-img-top"
                                            alt="<?= $row['name'] ?>">
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


<?php
include("./includes/footer.php")
    ?>