<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Add SweetAlert CDN -->
    <style>
    /* Custom Styles */
    body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
        background-color: #007bff;
        position: sticky;
        top: 0;
        z-index: 1030;
    }

    .navbar .btn-danger {
        background-color: #dc3545;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.2);
    }

    .card-body {
        padding: 20px;
        background-color: #fff;
    }

    .card-title {
        font-size: 1.35rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .card-text {
        font-size: 0.95rem;
        line-height: 1.6;
        color: #555;
    }

    .badge {
        font-size: 0.9rem;
        border-radius: 12px;
        padding: 6px 12px;
    }

    .alert {
        font-size: 1.1rem;
        border-radius: 8px;
    }

    .btn-custom {
        background-color: #007bff;
        color: white;
    }

    .btn-custom:hover {
        background-color: #0056b3;
    }

    .btn-sm {
        padding: 6px 14px;
    }

    .navbar-brand {
        color: white;
        font-size: 1.5rem;
    }

    .container {
        margin-top: 50px;
    }

    /* Responsive Cards */
    @media (max-width: 768px) {
        .col-md-4 {
            max-width: 100%;
            margin-bottom: 15px;
        }
    }

    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }

    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
    }
    </style>
</head>

<body>
    <!-- Navigation Bar with Logout -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <form action="/logout" method="POST" class="d-flex">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center mb-4">Welcome to Your Dashboard</h1>

        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); endif; ?>

        <?php if (!empty($trips)): ?>
        <div class="row">
            <?php foreach ($trips as $trip): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($trip['trip_name']) ?></h5>
                        <p class="card-text">
                            <strong>Start Date:</strong> <?= htmlspecialchars($trip['start_date']) ?><br>
                            <strong>End Date:</strong> <?= htmlspecialchars($trip['end_date']) ?><br>
                            <strong>Budget:</strong> $<?= htmlspecialchars($trip['budget']) ?><br>
                            <strong>Status:</strong>
                            <a href="/participant/trip-details/<?= $trip['trip_id']; ?>"
                                class="btn btn-info btn-sm">View Details</a>
                            <span
                                class="badge bg-<?= ($trip['status'] === 'accepted') ? 'success' : (($trip['status'] === 'declined') ? 'danger' : 'secondary'); ?>">
                                <?= htmlspecialchars($trip['status'] ?? 'Pending') ?>
                            </span>
                            <br>
                            <?php if (!empty($trip['responded_at'])): ?>
                            <small class="text-muted">Responded at:
                                <?= htmlspecialchars($trip['responded_at']) ?></small>
                            <?php endif; ?>
                        </p>

                        <!-- Show Accept and Decline buttons only if status is pending -->
                        <?php if ($trip['status'] === 'pending'): ?>
                        <form method="POST" action="/participant/update-status">
                            <input type="hidden" name="trip_id" value="<?= $trip['trip_id']; ?>">
                            <button type="submit" name="status" value="accepted"
                                class="btn btn-success btn-sm me-2">Accept</button>
                            <button type="submit" name="status" value="declined"
                                class="btn btn-danger btn-sm">Decline</button>
                        </form>
                        <?php endif; ?>
                        <!-- Check if the trip has expired and show message -->
                        <?php
        $currentDate = new DateTime();
        $endDate = new DateTime($trip['end_date']);
        if ($currentDate > $endDate): ?>
                        <div class="alert alert-danger mt-3" role="alert">
                            This trip has expired.
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="alert alert-warning text-center">
            No trips available for you at the moment.
        </div>
        <?php endif; ?>
    </div>

    <!-- Required Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- JavaScript for Upcoming Trip Alert -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php foreach ($trips as $trip): ?>
            (function() {
                var tripName = "<?= htmlspecialchars($trip['trip_name']) ?>";
                var startDateStr = "<?= $trip['start_date'] ?>";
                var status = "<?= $trip['status'] ?>";

                var startDate = new Date(startDateStr + "T00:00:00Z");
                var today = new Date();
                today.setUTCHours(0, 0, 0, 0);

                var timeDiff = startDate.getTime() - today.getTime();
                var daysRemaining = Math.floor(timeDiff / (1000 * 60 * 60 * 24));

                console.log(
                    `Trip: ${tripName}, Start Date: ${startDateStr}, Days Remaining: ${daysRemaining}, Status: ${status}`
                    );

                // Check if the trip has already started
                if (daysRemaining > 0 && daysRemaining <= 3 && status === 'accepted') {
                    console.log("Showing SweetAlert for upcoming trip: " + tripName);
                    Swal.fire({
                        title: "Upcoming Trip!",
                        text: `Your trip '${tripName}' starts on ${startDateStr}.`,
                        icon: "info",
                        confirmButtonText: "OK",
                        timer: 5000
                    });
                } else if (daysRemaining <= 0 && status === 'accepted') {
                    // Trip has already started, no alert needed
                    console.log("No alert needed. Trip has already started.");
                }
            })();
        <?php endforeach; ?>

    });
    </script>
</body>

</html>