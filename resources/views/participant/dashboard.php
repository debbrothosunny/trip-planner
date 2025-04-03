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

    <!-- Session message if set -->
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info text-center">
        <?= htmlspecialchars($_SESSION['message']) ?>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Display trips if available -->
    <?php if (!empty($trips)): ?>
    <div class="row">
        <?php 
    $currentDate = new DateTime(); 
    foreach ($trips as $trip): 
        $startDate = new DateTime($trip['start_date']);
        $endDate = new DateTime($trip['end_date']);
        $isTripStarted = $currentDate >= $startDate; // Check if the trip has started
        $isExpired = $currentDate > $endDate; // Check if trip is expired
    ?>
        <?php 
    // Only show 'pending' or 'accepted' trips that are not expired
    if (($trip['status'] === 'pending' || $trip['status'] === 'accepted') && !$isExpired): 
    ?>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($trip['trip_name']) ?></h5>
                    <p class="card-text">
                        <strong>Start Date:</strong> <?= htmlspecialchars($trip['start_date']) ?><br>
                        <strong>End Date:</strong> <?= htmlspecialchars($trip['end_date']) ?><br>
                        <strong>Budget:</strong> $<?= htmlspecialchars($trip['budget']) ?><br>
                        <strong>Created By:</strong> <?= htmlspecialchars($trip['creator_name']) ?>
                        (<?= htmlspecialchars($trip['creator_email']) ?>)<br>
                        <strong>Status:</strong>
                        <a href="/participant/trip-details/<?= $trip['trip_id']; ?>" class="btn btn-info btn-sm">View
                            Details</a>
                        <span
                            class="badge bg-<?= ($trip['status'] === 'accepted') ? 'success' : (($trip['status'] === 'declined') ? 'danger' : 'secondary'); ?>">
                            <?= htmlspecialchars($trip['status'] ?? 'Pending') ?>
                        </span>
                    </p>

                    <!-- Payment Status -->
                    <?php 
                $paymentStatus = $paymentModel->getPaymentStatus($userId, $trip['trip_id']);
                $paymentStatus = isset($paymentStatus) ? strtolower($paymentStatus) : 'unpaid';
                ?>

                    <!-- Countdown Logic -->
                    <?php
                $currentDateTime = new DateTime();
                $interval = $currentDateTime->diff($startDate);
                $remainingDays = $interval->d;
                $remainingHours = $interval->h;
                $remainingMinutes = $interval->i;

                $countdownClass = ($currentDateTime < $startDate) ? "text-primary" : "text-danger";
                ?>

                    <div class="analog-clock-container">
                        <div class="clock">
                            <div class="hand day-hand" id="dayHand"></div>
                            <div class="hand hour-hand" id="hourHand"></div>
                            <div class="hand minute-hand" id="minuteHand"></div>
                        </div>
                        <p class="card-text <?= $countdownClass; ?>">
                            <strong>Countdown:</strong>
                            <span id="countdownText<?= $trip['trip_id']; ?>">
                                <?= ($currentDateTime < $startDate) ? "Starts in: $remainingDays days, $remainingHours hours, $remainingMinutes minutes" : "Trip has already started."; ?>
                            </span>
                        </p>
                    </div>

                    <!-- Trip Status and Payment Status -->
                    <?php if ($trip['status'] === 'pending' && !$isExpired && !$isTripStarted): ?>
                    <form method="POST" action="/participant/update-status">
                        <input type="hidden" name="trip_id" value="<?= $trip['trip_id']; ?>">
                        <button type="submit" name="status" value="accepted"
                            class="btn btn-success btn-sm me-2">Accept</button>
                        <button type="submit" name="status" value="declined"
                            class="btn btn-danger btn-sm">Decline</button>
                    </form>
                    <?php elseif ($trip['status'] === 'accepted' && $isExpired): ?>
                    <div class="alert alert-success mt-3" role="alert">
                        <strong>Completed</strong>
                    </div>
                    <?php elseif ($isExpired): ?>
                    <div class="alert alert-danger mt-3" role="alert">
                        This trip has expired. You can no longer accept or decline.
                    </div>
                    <!-- Payment Status -->
                    <?php if ($paymentStatus === 'completed'): ?>
                    <span class="badge bg-success">You are joined in this trip</span>
                    <?php elseif ($paymentStatus === 'pending'): ?>
                    <span class="badge bg-warning text-dark">Payment Pending</span>
                    <div class="alert alert-info mt-3" role="alert">
                        Your payment is pending. Please wait for confirmation.
                    </div>
                    <?php elseif ($paymentStatus === 'unpaid'): ?>
                    <div class="alert alert-warning mt-3" role="alert">
                        Please make the payment first before the trip can proceed.
                    </div>
                    <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal"
                        data-bs-target="#paymentModal<?= $trip['trip_id']; ?>">Make Payment</button>
                    <?php endif; ?>

                    <!-- Payment Modal -->
                    <div class="modal fade" id="paymentModal<?= $trip['trip_id']; ?>" tabindex="-1"
                        aria-labelledby="paymentModalLabel<?= $trip['trip_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="paymentModalLabel<?= $trip['trip_id']; ?>">Make Payment
                                        for <?= htmlspecialchars($trip['trip_name']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="/participant/make-payment">
                                        <input type="hidden" name="trip_id" value="<?= $trip['trip_id']; ?>">
                                        <div class="mb-3">
                                            <label for="transaction_id" class="form-label">Transaction ID</label>
                                            <input type="text" class="form-control" name="transaction_id"
                                                placeholder="Enter your transaction ID" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Enter Payment Amount (৳)</label>
                                            <input type="number" class="form-control" name="amount" required min="1"
                                                max="<?= $trip['budget']; ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">Payment Method</label>
                                            <select class="form-control" name="payment_method" required>
                                                <option value="bkash">Bkash</option>
                                                <option value="nagad">Nagad</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-success">Submit Payment</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Payment Modal -->
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <?php endif; ?>
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


<script>
function startCountdown(tripId, startDate) {
    let countDownDate = new Date(startDate).getTime();

    let x = setInterval(function() {
        let now = new Date().getTime();
        let distance = countDownDate - now;

        if (distance > 0) {
            let days = Math.floor(distance / (1000 * 60 * 60 * 24));
            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('countdown' + tripId).innerHTML =
                `Starts in: ${days} days, ${hours} hours, ${minutes} minutes, ${seconds} seconds`;
        } else {
            clearInterval(x);
            document.getElementById('countdown' + tripId).innerHTML = "Trip has already started.";
        }
    }, 1000);
}

startCountdown(<?= $trip['trip_id']; ?>, "<?= $trip['start_date']; ?>");
</script>