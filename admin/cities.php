<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /CARE/auth/login.php");
    exit;
}

$message = "";

if (isset($_POST['add_city'])) {
    $name = trim($_POST['name']);
    if ($name) {
        $stmt = $conn->prepare("INSERT INTO cities (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $message = "City added successfully.";
    }
}

if (isset($_POST['edit_city'])) {
    $id = intval($_POST['city_id']);
    $name = trim($_POST['name']);
    if ($name && $id) {
        $stmt = $conn->prepare("UPDATE cities SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $message = "City updated.";
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM cities WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = "City deleted.";
}

$cities = $conn->query("SELECT * FROM cities ORDER BY name ASC");

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
<div class="container my-5">
    <h2 class="text-center text-secondary fw-bold mb-4">Manage Cities</h2>

    <?php if ($message): ?>
        <div class="alert alert-success text-center"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST" class="mb-4 d-flex gap-2 justify-content-center">
        <input type="text" name="name" class="form-control w-25" placeholder="Enter city name" required>
        <button type="submit" name="add_city" class="btn btn-success">Add City</button>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>City Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $cities->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Edit</button>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this city?')">Delete</a>
                            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit City</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="city_id" value="<?php echo $row['id']; ?>">
                                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="edit_city" class="btn btn-primary">Save</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php include('../includes/footer.php'); ?>
