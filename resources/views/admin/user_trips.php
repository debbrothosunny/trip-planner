<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Trips</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h2 class="mb-0">User's Trips</h2>
            </div>
            <div class="card-body">
                <h4 class="mb-3">Name: <span class="fw-bold"> <?= htmlspecialchars($data['user']['name']) ?> </span></h4>
                <h5>Email: <span class="fw-bold"> <?= htmlspecialchars($data['user']['email']) ?> </span></h5>

                <?php if (!empty($data['userTrips'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mt-4">
                        <thead class="table-dark">
                            <tr>
                                <th>Trip ID</th>
                                <th>Trip Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Budget</th>
                                <th>Accommodation Name</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Check-in Time</th>
                                <th>Check-out Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['userTrips'] as $trip): ?>
                            <tr>
                                <td><?= htmlspecialchars($trip['trip_id']) ?></td>
                                <td><?= htmlspecialchars($trip['trip_name']) ?></td>
                                <td><?= htmlspecialchars($trip['start_date']) ?></td>
                                <td><?= htmlspecialchars($trip['end_date']) ?></td>
                                <td><?= htmlspecialchars($trip['budget']) ?></td>
                                <td><?= htmlspecialchars($trip['accommodation_name']) ?></td>
                                <td><?= htmlspecialchars($trip['location']) ?></td>
                                <td><?= htmlspecialchars($trip['price']) ?></td>
                                <td><?= htmlspecialchars($trip['check_in_time']) ?></td>
                                <td><?= htmlspecialchars($trip['check_out_time']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-warning text-center mt-4" role="alert">
                    No trips found for this user.
                </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="/admin/dashboard" class="btn btn-primary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
