<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$message = "";

if (isset($_GET['delete'])) {
    $docId = intval($_GET['delete']);
    $get = $conn->prepare("SELECT user_id FROM doctors WHERE id = ?");
    $get->bind_param("i", $docId);
    $get->execute();
    $res = $get->get_result();
    if ($res->num_rows) {
        $uid = $res->fetch_assoc()['user_id'];
        $conn->query("DELETE FROM doctors WHERE id = $docId");
        $conn->query("DELETE FROM users WHERE id = $uid");
        $message = "Doctor deleted.";
    }
}

if (isset($_POST['add_doctor'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $image = "";

    $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        $userStmt = $conn->prepare("INSERT INTO users (username, email, password, role, address, phone) VALUES (?, ?, ?, 'doctor', ?, ?)");
        $userStmt->bind_param("sssss", $username, $email, $password, $address, $phone);
        $userStmt->execute();
        $userId = $userStmt->insert_id;

        $docStmt = $conn->prepare("INSERT INTO doctors (user_id, name, specialization, experience, image) VALUES (?, ?, ?, ?, ?)");
        $docStmt->bind_param("issis", $userId, $name, $specialization, $experience, $image);
        $docStmt->execute();

        $message = "Doctor added.";
    } else {
        $message = "Username or Email already exists.";
    }
}

if (isset($_POST['edit_doctor'])) {
    $docId = $_POST['doc_id'];
    $name = $_POST['edit_name'];
    $specialization = $_POST['edit_specialization'];
    $experience = $_POST['edit_experience'];
    $address = $_POST['edit_address'];
    $phone = $_POST['edit_phone'];
    $email = $_POST['edit_email'];

    $getUser = $conn->prepare("SELECT user_id FROM doctors WHERE id = ?");
    $getUser->bind_param("i", $docId);
    $getUser->execute();
    $uid = $getUser->get_result()->fetch_assoc()['user_id'];

    $stmt = $conn->prepare("UPDATE doctors SET name=?, specialization=?, experience=? WHERE id=?");
    $stmt->bind_param("ssii", $name, $specialization, $experience, $docId);
    $stmt->execute();


    $stmt = $conn->prepare("UPDATE users SET email=?, address=?, phone=? WHERE id=?");
    $stmt->bind_param("sssi", $email, $address, $phone, $uid);
    $stmt->execute();


    $message = "Doctor updated.";
}

$doctors = $conn->query("SELECT d.*, u.email, u.phone, u.address, u.username FROM doctors d JOIN users u ON d.user_id = u.id WHERE u.role = 'doctor'");
?>

<?php include('../includes/header.php'); ?>

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
        <h2 class="text-center mb-4 fw-bold text-secondary">Manage Doctors</h2>

        <?php if ($message): ?>
            <div class="alert alert-info text-center"><?= $message ?></div>
        <?php endif; ?>


        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Specialization</th>
                        <th>Experience</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($doc = $doctors->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['name']) ?></td>
                            <td><?= htmlspecialchars($doc['username']) ?></td>
                            <td><?= htmlspecialchars($doc['email']) ?></td>
                            <td><?= htmlspecialchars($doc['specialization']) ?></td>
                            <td><?= htmlspecialchars($doc['experience']) ?> yrs</td>
                            <td><?= htmlspecialchars($doc['phone']) ?></td>
                            <td><?= htmlspecialchars($doc['address']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#editModal<?= $doc['id'] ?>">Edit</button>
                                <a href="?delete=<?= $doc['id'] ?>" onclick="return confirm('Delete this doctor?')"
                                    class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?= $doc['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" class="modal-content">
                                    <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Doctor</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-2">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="edit_name" class="form-control"
                                                value="<?= htmlspecialchars($doc['name']) ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Specialization</label>
                                            <input type="text" name="edit_specialization" class="form-control"
                                                value="<?= htmlspecialchars($doc['specialization']) ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Experience (years)</label>
                                            <input type="number" name="edit_experience" class="form-control"
                                                value="<?= htmlspecialchars($doc['experience']) ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="edit_email" class="form-control"
                                                value="<?= htmlspecialchars($doc['email']) ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="edit_phone" class="form-control"
                                                value="<?= htmlspecialchars($doc['phone']) ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Address</label>
                                            <input type="text" name="edit_address" class="form-control"
                                                value="<?= htmlspecialchars($doc['address']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="edit_doctor" class="btn btn-success">Save
                                            Changes</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <button class="btn btn-primary mb-3 " data-bs-toggle="modal" data-bs-target="#addModal">+ Add Doctor</button>

        <div class="modal fade" id="addModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Doctor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Specialization</label>
                            <input type="text" name="specialization" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Experience (years)</label>
                            <input type="number" name="experience" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_doctor" class="btn btn-primary">Add Doctor</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>