<?php
// Ensure the session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accommodation List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Add SweetAlert CDN -->
</head>

<style>
.sidebar {
    width: 250px;
    height: 100vh;
    background: #343a40;
    color: white;
    padding: 20px;
    position: fixed;
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 10px;
    margin-bottom: 10px;
    background: #495057;
    border-radius: 5px;
    text-align: center;
}

.sidebar a:hover {
    background: #6c757d;
}

.content {
    margin-left: 270px;
    padding: 20px;
    width: 100%;
}
</style>

<body>
    <div class="sidebar">
        <h4>Dashboard</h4>

        <a href="/user/dashboard">Trip</a>
        <a href="/user/transportation">Transportation</a>
        <a href="/user/accommodation">Accommodation</a>
        <a href="/user/expense">Expense</a>
        <a href="/user/budget-view ">Budget Track</a>
        <a href="/user/my_trip_participants">Trip Participant</a>
        <nav class="navbar">
            <form action="/logout" method="POST">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </nav>
    </div>

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
                                class="btn btn-sm btn-warning">Edit</a>

                            <!-- Only keep the delete button without SweetAlert confirmation -->
                            <a href="#" onclick="confirmDelete(<?= $accommodation['id']; ?>)"
                                class="btn btn-sm btn-danger">Delete</a>
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

</body>

</html>