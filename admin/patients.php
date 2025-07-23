<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$message = "";

if (isset($_GET['delete'])) {
    $patId = intval($_GET['delete']);
    $get = $conn->prepare("SELECT user_id FROM patients WHERE id = ?");
    $get->bind_param("i", $patId);
    $get->execute();
    $res = $get->get_result();
    if ($res->num_rows) {
        $uid = $res->fetch_assoc()['user_id'];
        $conn->query("DELETE FROM patients WHERE id = $patId");
        $conn->query("DELETE FROM users WHERE id = $uid");
        $message = "Patient deleted.";
    }
}

if (isset($_POST['add_patient'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        $userStmt = $conn->prepare("INSERT INTO users (username, email, password, role, address, phone) VALUES (?, ?, ?, 'patient', ?, ?)");
        $userStmt->bind_param("sssss", $username, $email, $password, $address, $phone);
        $userStmt->execute();
        $userId = $userStmt->insert_id;

        $patStmt = $conn->prepare("INSERT INTO patients (user_id, name) VALUES (?, ?)");
        $patStmt->bind_param("is", $userId, $name);
        $patStmt->execute();

        $message = "Patient added.";
    } else {
        $message = "Username or Email already exists.";
    }
}


if (isset($_POST['edit_patient'])) {
    $patId = $_POST['pat_id'];
    $name = $_POST['edit_name'];
    $address = $_POST['edit_address'];
    $phone = $_POST['edit_phone'];
    $email = $_POST['edit_email'];

    $getUser = $conn->prepare("SELECT user_id FROM patients WHERE id = ?");
    $getUser->bind_param("i", $patId);
    $getUser->execute();
    $uid = $getUser->get_result()->fetch_assoc()['user_id'];

    $conn->prepare("UPDATE patients SET name=? WHERE id=?")
        ->bind_param("si", $name, $patId)->execute();

    $conn->prepare("UPDATE users SET email=?, address=?, phone=? WHERE id=?")
        ->bind_param("sssi", $email, $address, $phone, $uid)->execute();

    $message = "Patient updated.";
}

$patients = $conn->query("SELECT p.*, u.email, u.phone, u.address, u.username FROM patients p JOIN users u ON p.user_id = u.id WHERE u.role = 'patient'");
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
    <h2 class="text-center mb-4 fw-bold text-secondary">Manage Patients</h2>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
    <?php endif; ?>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Patient</button>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($pat = $patients->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($pat['name']) ?></td>
                        <td><?= htmlspecialchars($pat['username']) ?></td>
                        <td><?= htmlspecialchars($pat['email']) ?></td>
                        <td><?= htmlspecialchars($pat['phone']) ?></td>
                        <td><?= htmlspecialchars($pat['address']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $pat['id'] ?>">Edit</button>
                            <a href="?delete=<?= $pat['id'] ?>" onclick="return confirm('Delete this patient?')"
                                class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>


                    <div class="modal fade" id="editModal<?= $pat['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" class="modal-content">
                                <input type="hidden" name="pat_id" value="<?= $pat['id'] ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Patient</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-2">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="edit_name" class="form-control"
                                            value="<?= htmlspecialchars($pat['name']) ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="edit_email" class="form-control"
                                            value="<?= htmlspecialchars($pat['email']) ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="edit_phone" class="form-control"
                                            value="<?= htmlspecialchars($pat['phone']) ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Address</label>
                                        <input type="text" name="edit_address" class="form-control"
                                            value="<?= htmlspecialchars($pat['address']) ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="edit_patient" class="btn btn-success">Save Changes</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_patient" class="btn btn-primary">Add Patient</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
</main>

<?php include('../includes/footer.php'); ?>