<?php
$header_title = "Accomodation";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
?>

<style>
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

<div class="container mt-4">
    <h2 class="text-center mb-4">Accommodation List</h2>

    <div class="d-flex justify-content-between mb-3">
        <a href="/user/accommodation/create" class="btn btn-primary">Add New Accommodation</a>
    </div>

    <!-- Check if there is any SweetAlert session variable -->
    <?php if (isset($_SESSION['sweetalert'])): ?>
    <script>
    Swal.fire({
        title: '<?= $_SESSION['sweetalert']['title']; ?>',
        text: '<?= $_SESSION['sweetalert']['text']; ?>',
        icon: '<?= $_SESSION['sweetalert']['icon']; ?>',
        confirmButtonText: 'OK'
    });
    </script>
    <?php unset($_SESSION['sweetalert']); // Unset to ensure it doesn't display again ?>
    <?php endif; ?>

    <?php if (!empty($accommodations)): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Hotel Name</th>
                    <th>Trip Name</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Amenities</th>
                    <th>Check-in Time</th>
                    <th>Check-out Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accommodations as $index => $accommodation): ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td><?= htmlspecialchars($accommodation['name']); ?></td>
                    <td><?= htmlspecialchars($accommodation['trip_name']); ?></td>
                    <td><?= htmlspecialchars($accommodation['location']); ?></td>
                    <td>$<?= number_format($accommodation['price'], 2); ?></td>
                    <td><?= htmlspecialchars($accommodation['amenities']); ?></td>
                    <td><?= htmlspecialchars(date("Y-m-d h:i A", strtotime($accommodation['check_in_time']))); ?>
                    </td>
                    <td><?= htmlspecialchars(date("Y-m-d h:i A", strtotime($accommodation['check_out_time']))); ?>
                    </td>

                    <td>
                        <a href="/user/accommodation/<?= $accommodation['id']; ?>/edit"
                            class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>

                        <!-- Only keep the delete button without SweetAlert confirmation -->
                        <a href="#" onclick="confirmDelete(<?= $accommodation['id']; ?>)"
                            class="btn btn-sm btn-danger"> <i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="alert alert-warning text-center">No accommodations found.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function confirmDelete(id) {
    // SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: 'You cannot revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // If confirmed, redirect to the delete route
            window.location.href = '/user/accommodation/delete/' + id;
        }
    });
}
</script>