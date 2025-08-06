<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /CARE/auth/login.php');
    exit;
}

$message = "";

if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $userId");
    $conn->query("DELETE FROM doctors WHERE user_id = $userId");
    $conn->query("DELETE FROM patients WHERE user_id = $userId");
    $message = "User deleted.";
}

if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, address, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $email, $password, $role, $address, $phone);
        $stmt->execute();
        $message = "User added.";
    } else {
        $message = "Email or Username already exists.";
    }
}

if (isset($_POST['edit_user'])) {
    $id = $_POST['user_id'];
    $email = $_POST['edit_email'];
    $username = $_POST['edit_username'];
    $role = $_POST['edit_role'];
    $address = $_POST['edit_address'];
    $phone = $_POST['edit_phone'];

    $stmt = $conn->prepare("UPDATE users SET email=?, username=?, role=?, address=?, phone=? WHERE id=?");
    $stmt->bind_param("sssssi", $email, $username, $role, $address, $phone, $id);
    $stmt->execute();
    $message = "User updated.";
}

$users = $conn->query("SELECT * FROM users");
?>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/header.php"); ?>
<style>
    main { min-height: 90vh; }
    .container { text-align: center; }
</style>

<main>
<div class="container my-5">
    <h2 class="text-center mb-4 fw-bold text-secondary">Manage Users</h2>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
    <?php endif; ?>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Add User</button>

    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="table-light">
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td><?= htmlspecialchars($u['phone']) ?></td>
                        <td><?= htmlspecialchars($u['address']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $u['id'] ?>">Edit</button>
                            <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Delete this user?')" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>

                    <div class="modal fade" id="editModal<?= $u['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <div class="mb-2">
                                        <label>Email</label>
                                        <input type="email" name="edit_email" class="form-control"
                                            value="<?= htmlspecialchars($u['email']) ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Username</label>
                                        <input type="text" name="edit_username" class="form-control"
                                            value="<?= htmlspecialchars($u['username']) ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Role</label>
                                        <select name="edit_role" class="form-control" required>
                                            <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="doctor" <?= $u['role'] == 'doctor' ? 'selected' : '' ?>>Doctor</option>
                                            <option value="patient" <?= $u['role'] == 'patient' ? 'selected' : '' ?>>Patient</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label>Phone</label>
                                        <input type="text" name="edit_phone" class="form-control"
                                            value="<?= htmlspecialchars($u['phone']) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label>Address</label>
                                        <input type="text" name="edit_address" class="form-control"
                                            value="<?= htmlspecialchars($u['address']) ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="edit_user" class="btn btn-success">Save</button>
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
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="admin">Admin</option>
                            <option value="doctor">Doctor</option>
                            <option value="patient">Patient</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/CARE/includes/footer.php"); ?>
