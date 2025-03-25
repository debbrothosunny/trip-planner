<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Hotel Bookings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Optional Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Pending Hotel Bookings</h2>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if (!empty($bookings)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>User</th>
                                        <th>Hotel</th>

                                        <th>Location</th>
                                        <th>Hotel Description</th>
                                        <th>Room Type</th>
                                        <th>Price</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($booking['user_name']) ?></td>
                                        <td><?= htmlspecialchars($booking['hotel_name']) ?></td>
                                        <td><?= htmlspecialchars($booking['location']) ?></td>
                                        <td><?= htmlspecialchars($booking['hotel_description']) ?></td>
                                        <td><?= htmlspecialchars($booking['room_type']) ?></td>
                                        <td>$<?= number_format($booking['price'], 2) ?></td>
                                        <td><?= htmlspecialchars($booking['check_in_date']) ?></td>
                                        <td><?= htmlspecialchars($booking['check_out_date']) ?></td>
                                        <td>
                                            <!-- Check if the booking is confirmed or not -->
                                            <?php if ($booking['status'] == 0): // If status is 0, it means pending ?>
                                            <form method="POST" action="/admin/hotel-bookings/confirm">
                                                <input type="hidden" name="accommodation_id"
                                                    value="<?= $booking['id'] ?>">
                                                <input type="hidden" name="hotel_id"
                                                    value="<?= $booking['hotel_id'] ?>">
                                                <input type="hidden" name="room_type"
                                                    value="<?= $booking['room_type'] ?>">
                                                <input type="hidden" name="total_rooms"
                                                    value="<?= $booking['total_rooms'] ?>">
                                                <button type="submit" class="btn btn-success btn-sm">Confirm</button>
                                            </form>

                                            <?php else: // If status is 1, it means confirmed ?>
                                            <button class="btn btn-success btn-sm" disabled>Confirmed</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">No pending hotel bookings found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS (Optional for interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>