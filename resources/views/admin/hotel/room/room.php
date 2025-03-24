<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hotel Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">Hotel Rooms</h2>
            <a href="/admin/hotels/rooms/create" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Add New Room
            </a>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <!-- <div class="input-group mb-4">
            <input type="text" class="form-control" placeholder="Search by hotel name..." id="searchInput">
            <button class="btn btn-primary">Search</button>
        </div> -->

        <!-- Hotel Rooms Table -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Hotel Name</th>
                        <th>Room Type</th>
                        <th>Price</th>
                        <th>Total Rooms</th>
                        <th>Available Rooms</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room) : ?>
                    <tr>
                        <td><?= $room['id'] ?></td>
                        <td><?= htmlspecialchars($room['hotel_name']) ?></td>
                        <td><?= htmlspecialchars($room['room_type']) ?></td>
                        <td class="text-success fw-bold">$<?= number_format($room['price'], 2) ?></td>
                        <td><?= $room['total_rooms'] ?></td>
                        <td><?= $room['available_rooms'] ?></td>
                        <td><?= htmlspecialchars($room['description']) ?></td>
                        <td>
                            <a href="/admin/hotels/rooms/edit/<?= $room['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>

                            <a href="/admin/hotels/rooms/delete/<?= $room['id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-4">
            <a href="/admin/dashboard" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>

    <!-- Bootstrap 5 Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>