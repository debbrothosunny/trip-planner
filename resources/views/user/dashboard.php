<?php
$header_title = "Trip";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
?>

<style>
body {
    display: flex;

}

.sidebar {
    width: 250px;
    background: #2c3e50;
    color: white;
    height: 100vh;
    position: fixed;
    padding-top: 20px;
}  

.sidebar a {
    color: white;
    display: flex;
    align-items: center;
    padding: 12px;
    text-decoration: none;
    transition: 0.3s;
}

.sidebar a i {
    margin-right: 10px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #34495e;
}

.content {
    margin-left: 270px;
    padding: 20px;
    width: 100%;
}
</style>





<div class="content">
    <div class="container mt-4">
        <h2 class="mb-3">Trip Dashboard</h2>
        <a href="/user/create-trip" class="btn btn-success mb-3">+ Add New Trip</a>

        <?php if ($isNewUser): ?>
        <div class="alert alert-info">Welcome! Start by creating your first trip.</div>
        <?php elseif (!empty($trips)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Trip Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Budget</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                <tr>
                    <td><?= $trip['id']; ?></td>
                    <td><?= htmlspecialchars($trip['name']); ?></td>
                    <td><?= $trip['start_date']; ?></td>
                    <td><?= $trip['end_date']; ?></td>
                    <td>$<?= number_format($trip['budget'], 2); ?></td>
                    <td class="d-flex justify-content-center">
                        <a href="/user/trip/<?= $trip['id']; ?>/edit" class="btn btn-warning btn-sm me-2">Edit</a>
                        <a href="/trip/<?= $trip['id'] ?>/itinerary" class="btn btn-success btn-sm me-2">Trip
                            Itinerary</a>
                        <!-- <a href="/user/trip/<?= $trip['id']; ?>/invitation/send" class="btn btn-primary btn-sm me-2">Send Invitation</a> -->
                        <a href="/user/trip/delete/<?= $trip['id']; ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-warning">No trips found.</div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['success'])) {
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: '{$_SESSION['success']}',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '/user/dashboard';
            });
        </script>";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: '{$_SESSION['error']}',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '/user/dashboard';
            });
        </script>";
        unset($_SESSION['error']);
    }
    ?>