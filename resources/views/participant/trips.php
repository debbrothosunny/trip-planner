<?php
$header_title = "Trip";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
?>
<style>
body {
    display: flex;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f7fa;
}

.content {
    margin-left: 270px;
    padding: 30px 40px;
    width: 100%;
}

.navbar {
    background-color: #007bff;
    position: sticky;
    top: 0;
    z-index: 1030;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.navbar .btn-danger {
    background-color: #dc3545;
    border: none;
}

.card {
    border-radius: 16px;
    background-color: #1c1c1c;
    color: #fff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none;
    padding: 30px;
    min-height: 250px;
    width: 100%;
}

.card:hover {

    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.5);
}

.card-body {
    padding: 30px;
    font-size: 1.1rem;
}

.card a {
    color: #0dcaf0;
}

.card .badge {
    background-color: #444;
    color: #fff;
}

.card-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 12px;
}

.card-text {
    font-size: 1rem;
    line-height: 1.6;
    color: #cccccc;
}

.badge {
    font-size: 0.85rem;
    border-radius: 20px;
    padding: 6px 14px;
}

.alert {
    font-size: 1rem;
    border-radius: 10px;
    padding: 15px;
    color: #fff;
    background-color: #2c2c2c;
    border: 1px solid #444;
}

.btn-custom {
    background-color: #007bff;
    color: white;
    border: none;
}

.btn-custom:hover {
    background-color: #0056b3;
}

.btn-sm {
    padding: 6px 14px;
    font-size: 0.85rem;
}

.modal-content {
    border-radius: 12px;
    background-color: #1c1c1c;
    color: #fff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.analog-clock-container {
    margin-top: 10px;
    margin-bottom: 10px;
    font-size: 0.9rem;
    color: #fff;
}

.container {
    margin-top: 60px;
}

@media (max-width: 768px) {
    .col-md-4 {
        max-width: 100%;
        margin-bottom: 20px;
    }

    .content {
        margin-left: 0;
        padding: 20px;
    }

    .sidebar {
        position: relative;
        height: auto;
        width: 100%;
    }
}
</style>

<div class="container">
    <?php if (isset($_SESSION['message'])) : ?>
    <div class="alert alert-info text-center">
        <?= htmlspecialchars($_SESSION['message']) ?>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="mb-4">
        <div class="row g-3 align-items-center mb-2">
            <div class="col-md-3">
                <label for="trip_style_filter" class="form-label">Trip Style:</label>
                <select class="form-select" id="trip_style_filter" name="trip_style">
                    <option value="">All Styles</option>
                    <?php foreach ($uniqueTripStyles as $style): ?>
                    <option value="<?= htmlspecialchars($style); ?>"
                        <?= ($tripStyleFilter === $style) ? 'selected' : '' ?>>
                        <?= htmlspecialchars(ucfirst($style)); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label for="min_budget" class="form-label">Min Budget:</label>
                <input type="number" class="form-control" id="min_budget"
                    placeholder="Enter minimum budget (e.g. 500 $)" name="min_budget"
                    value="<?= htmlspecialchars($_GET['min_budget'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label for="max_budget" class="form-label">Max Budget:</label>
                <input type="number" class="form-control" id="max_budget"
                    placeholder="Enter maximum budget (e.g. 2000 $)" name="max_budget"
                    value="<?= htmlspecialchars($_GET['max_budget'] ?? '') ?>">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <form method="GET" action="/participant/trips" class="d-flex gap-2" id="filterForm">
                    <input type="hidden" name="min_budget" value="<?= htmlspecialchars($_GET['min_budget'] ?? '') ?>">
                    <input type="hidden" name="max_budget" value="<?= htmlspecialchars($_GET['max_budget'] ?? '') ?>">
                    <input type="hidden" name="trip_style" id="hidden_trip_style"
                        value="<?= htmlspecialchars($_GET['trip_style'] ?? '') ?>">
                    <a href="/participant/trips" class="btn btn-outline-secondary mt-4">Clear</a>
                </form>
            </div>
        </div>





        <?php if (!empty($trips)) : ?>
        <div class="row" id="tripsContainer">
            <?php
            $currentDate = new DateTime();
            foreach ($trips as $trip) :
                $startDate = new DateTime($trip['start_date']);
                $endDate = new DateTime($trip['end_date']);

                $diffToEndDate = $currentDate->diff($endDate);
                $daysToEndDate = (int) $diffToEndDate->format('%r%a');
                $isWithinThreeDays = ($daysToEndDate <= 3);

                if ($isWithinThreeDays) {
                    continue;
                }
            ?>

            <?php if (($trip['status'] === 'pending' || $trip['status'] === 'accepted')) : ?>
            <div class="col-md-6 col-lg-4 mb-4 trip-card" data-budget="<?= htmlspecialchars($trip['budget']) ?>">
                <div class="card h-100 shadow-sm rounded-4">

                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center">
                            <a href="/user/profile/details/<?= $trip['creator_id']; ?>" class="me-2"
                                title="Click to view user details">
                                <img src="/<?= htmlspecialchars($trip['creator_profile_photo']) ?>"
                                    alt="<?= htmlspecialchars($trip['creator_name']) ?>'s Profile"
                                    class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </a>
                        </div>

                        <div>
                            <h5 class="card-title fw-bold mb-3"><?= htmlspecialchars($trip['trip_name']) ?></h5>
                            <p class="card-text mb-2">
                                <strong>Start:</strong> <?= htmlspecialchars($trip['start_date']) ?><br>
                                <strong>End:</strong> <?= htmlspecialchars($trip['end_date']) ?><br>
                                <strong>Budget:</strong> $<?= htmlspecialchars($trip['budget']) ?><br>
                                <strong>Trip Style:</strong> <?= htmlspecialchars($trip['trip_style']) ?? 'N/A' ?><br>
                                <strong>Creator:</strong> <?= htmlspecialchars($trip['creator_name']) ?>
                                (<?= htmlspecialchars($trip['creator_email']) ?>)<br>
                                <strong>Country:</strong> <?= htmlspecialchars($trip['country']) ?? 'N/A' ?><br>
                                <strong>City:</strong> <?= htmlspecialchars($trip['city']) ?? 'N/A' ?><br>
                                <strong>Total Participants:</strong>
                                <?= isset($trip['accepted_participants']) ? htmlspecialchars($trip['accepted_participants']) : 'N/A' ?>
                            </p>

                            <div class="mb-3">
                                <a href="/participant/trip-details/<?= $trip['trip_id']; ?>"
                                    class="btn btn-outline-info btn-sm me-2 mb-2">View Details</a>
                                <span
                                    class="badge bg-<?= ($trip['status'] === 'accepted') ? 'success' : (($trip['status'] === 'declined') ? 'danger' : 'secondary'); ?>">
                                    <?= htmlspecialchars($trip['status'] ?? 'Pending') ?>
                                </span>

                                <?php if ($trip['status'] === 'accepted' && $currentDate < $startDate) : ?>
                                <?php
                                $paymentStatus = $payment_status[$trip['trip_id']] ?? null;
                                if ($paymentStatus === '1') : ?>
                                <button type="button" class="btn btn-warning btn-sm w-100" disabled>Payment
                                    Pending</button>
                                <?php elseif ($paymentStatus === 'accepted') : ?>
                                <span class="badge bg-success">Payment Accepted</span>
                                <?php elseif ($paymentStatus === 'cancelled') : ?>
                                <span class="badge bg-danger">Payment Cancelled</span>
                                <?php elseif ($paymentStatus === '0' || $paymentStatus === 0) : ?>
                                <span class="badge bg-info">Joined</span>
                                <?php else : ?>
                                <button type="button" class="btn btn-primary btn-sm w-100 initiate-payment-btn"
                                    data-trip-id="<?= $trip['trip_id'] ?>" data-amount="<?= $trip['budget'] ?>">
                                    Make Payment
                                </button>
                                <div id="paypal-button-container-<?= $trip['trip_id'] ?>" style="display:none;"></div>
                                <p id="result-message-<?= $trip['trip_id'] ?>"></p>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <?php
        $currentDateTime = new DateTime();
        $startDate = new DateTime($trip['start_date']);
        $interval = $currentDateTime->diff($startDate);
        $remainingDays = $interval->d;
        $remainingHours = $interval->h;
        $remainingMinutes = $interval->i;
        $countdownClass = ($currentDateTime < $startDate) ? "text-primary" : "text-danger";
    ?>

                            <div class="analog-clock-container mb-3">
                                <div class="clock mb-2">
                                    <div class="hand day-hand" id="dayHand"></div>
                                    <div class="hand hour-hand" id="hourHand"></div>
                                    <div class="hand minute-hand" id="minuteHand"></div>
                                </div>
                                <p class="card-text <?= $countdownClass; ?>">
                                    <strong>Countdown:</strong>
                                    <span id="countdown<?= $trip['trip_id']; ?>">
                                        <?php
                    if ($currentDateTime < $startDate) {
                        echo "Starts in: $remainingDays days, $remainingHours hours, $remainingMinutes minutes";
                    } else {
                        echo "Trip has already started.";
                    }
                ?>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 d-grid gap-2">
                            <?php if ($trip['status'] === 'pending' && $currentDateTime < $startDate) : ?>
                            <form method="POST" action="/participant/update-status" class="d-flex gap-2">
                                <input type="hidden" name="trip_id" value="<?= $trip['trip_id']; ?>">
                                <button type="submit" name="status" value="accepted"
                                    class="btn btn-success btn-sm w-50">Accept</button>
                                <button type="submit" name="status" value="declined"
                                    class="btn btn-danger btn-sm w-50">Decline</button>
                            </form>
                            <?php elseif ($trip['status'] === 'accepted' && $currentDateTime < $startDate) : ?>
                            <form method="POST" action="/participant/cancel-trip" class="mt-2">
                                <input type="hidden" name="trip_id" value="<?= $trip['trip_id']; ?>">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 cancel-trip-btn"
                                    data-trip-id="<?= $trip['trip_id']; ?>">Cancel Trip</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1) : ?>
        <nav aria-label="Trip Pagination">
            <ul class="pagination justify-content-center mt-4">
                <?php if ($currentPage > 1) : ?>
                <li class="page-item">
                    <a class="page-link"
                        href="/participant/trips?page=<?= $currentPage - 1 ?><?= isset($_GET['min_budget']) ? '&min_budget=' . htmlspecialchars($_GET['min_budget']) : '' ?><?= isset($_GET['max_budget']) ? '&max_budget=' . htmlspecialchars($_GET['max_budget']) : '' ?>">Previous</a>
                </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?= ($i == $currentPage) ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="/participant/trips?page=<?= $i ?><?= isset($_GET['min_budget']) ? '&min_budget=' . htmlspecialchars($_GET['min_budget']) : '' ?><?= isset($_GET['max_budget']) ? '&max_budget=' . htmlspecialchars($_GET['max_budget']) : '' ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages) : ?>
                <li class="page-item">
                    <a class="page-link"
                        href="/participant/trips?page=<?= $currentPage + 1 ?><?= isset($_GET['min_budget']) ? '&min_budget=' . htmlspecialchars($_GET['min_budget']) : '' ?><?= isset($_GET['max_budget']) ? '&max_budget=' . htmlspecialchars($_GET['max_budget']) : '' ?>">Next</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <?php else : ?>
        <p class="mt-3">No trips found for you.</p>
        <?php endif; ?>
    </div>

    <script
        src="https://www.paypal.com/sdk/js?client-id=Ac_ZknBSSa3YAtxDeH5twQpjzYsQRT19gfbCQL1KMctSNV045R2ox64T5NeVoe3k8NXLG6HA8Rq58f8Y&buyer-country=US&currency=USD&components=buttons&enable-funding=card"
        data-sdk-integration-source="developer-studio"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const minBudgetInput = document.getElementById('min_budget');
        const maxBudgetInput = document.getElementById('max_budget');
        const tripStyleFilter = document.getElementById('trip_style_filter');
        const filterForm = document.getElementById('filterForm'); // Get the form element
        const tripCards = document.querySelectorAll('.trip-card');

        function filterTrips() {
            const minBudget = parseFloat(minBudgetInput.value) || 0;
            const maxBudget = parseFloat(maxBudgetInput.value) || Infinity;

            tripCards.forEach(card => {
                const budget = parseFloat(card.dataset.budget);
                if (budget >= minBudget && budget <= maxBudget) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Listen for changes on the trip style dropdown and submit the form directly
        tripStyleFilter.addEventListener('change', function() {
            // Sync budget inputs into hidden form inputs before submitting
            document.querySelector('input[name="min_budget"]').value = minBudgetInput.value;
            document.querySelector('input[name="max_budget"]').value = maxBudgetInput.value;
            document.querySelector('input[name="trip_style"]').value = tripStyleFilter.value;

            filterForm.submit();
        });

        // Listen for real-time changes on the budget inputs
        minBudgetInput.addEventListener('input', filterTrips);
        maxBudgetInput.addEventListener('input', filterTrips);

        <?php foreach ($trips as $trip): ?>
        startCountdown(<?= json_encode($trip['trip_id']); ?>, <?= json_encode($trip['start_date']); ?>);
        <?php endforeach; ?>

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
                    document.getElementById('countdown' + tripId).innerHTML =
                        "Trip has already started.";
                }
            }, 1000);
        }

        const cancelButtons = document.querySelectorAll('.cancel-trip-btn');
        cancelButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                const form = this.closest('form');
                const tripId = form.querySelector('input[name="trip_id"]').value;
                const cardBody = this.closest('.card-body');
                const tripCard = this.closest('.trip-card'); // Get the parent trip card

                Swal.fire({
                    title: 'Confirm Cancellation',
                    text: 'Are you sure you want to cancel your participation in this trip?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, cancel!',
                    cancelButtonText: 'No, go back'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Cancelling...',
                            text: 'Please wait while we process your cancellation.',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const formData = new FormData(form);
                        fetch('/participant/cancel-trip', {
                                method: 'POST',
                                body: formData,
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                Swal.close();
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Cancelled!',
                                        text: data.message ||
                                            'Your trip has been cancelled.',
                                        icon: 'success',
                                        showCloseButton: true
                                    }).then(() => {
                                        // Remove the trip card from the UI
                                        if (tripCard) {
                                            tripCard.remove();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Cancellation Failed',
                                        text: data.message ||
                                            'An error occurred while cancelling.',
                                        icon: 'error',
                                        showCloseButton: true
                                    });
                                }
                            })
                            .catch((error) => {
                                Swal.close();
                                Swal.fire({
                                    title: 'Error',
                                    text: 'An error occurred during cancellation.',
                                    icon: 'error',
                                    showCloseButton: true
                                });
                                console.error('Error:', error);
                            });
                    }
                });
            });
        });


        const initiatePaymentButtons = document.querySelectorAll('.initiate-payment-btn');
        initiatePaymentButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tripId = this.dataset.tripId;
                const amount = this.dataset.amount;
                const paypalButtonContainer = document.getElementById(
                    `paypal-button-container-${tripId}`);
                const resultMessageContainer = document.getElementById(
                    `result-message-${tripId}`);

                paypalButtonContainer.style.display = 'block';

                window.paypal.Buttons({
                    style: {
                        shape: "rect",
                        layout: "vertical",
                        color: "gold",
                        label: "paypal",
                    },
                    async createOrder(data, actions) {
                        try {
                            if (!amount) {
                                throw new Error("Amount is not defined");
                            }
                            return actions.order.create({
                                purchase_units: [{
                                    amount: {
                                        value: amount,
                                    },
                                }, ],
                            });
                        } catch (error) {
                            console.error("Create Order Error", error);
                            throw error; // Important: re-throw the error to PayPal
                        }
                    },
                    async onApprove(data, actions) {
                        try {
                            const captureResult = await actions.order.capture();
                            console.log("Capture Result:", captureResult);

                            if (captureResult.status === "COMPLETED") {
                                // Handle the successful payment on the client side.
                                resultMessageContainer.innerHTML =
                                    `Payment of $${amount} USD for Trip ${tripId}  was successful!`;
                                sendPaymentDetailsToServer(tripId, amount,
                                    captureResult.id, captureResult.payer);
                            } else {
                                resultMessageContainer.innerHTML =
                                    "Payment could not be completed. Please try again.";
                            }
                        } catch (error) {
                            console.error("On Approve Error", error);
                            resultMessageContainer.innerHTML =
                                "Payment could not be completed. Please try again.";

                        }
                    },
                    onError: (err) => {
                        // Handle any errors that occur during the PayPal transaction
                        console.error("PayPal Error:", err);
                        resultMessageContainer.innerHTML =
                            "An error occurred during the payment process. Please try again.";
                    },
                }).render(`#paypal-button-container-${tripId}`);
            });
        });
    });


    function sendPaymentDetailsToServer(tripId, amount, transactionId, payerInfo) {
        //  Send an AJAX request to your server to record the payment details
        fetch('/handle-payment-success', { //  Update this URL
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    trip_id: tripId,
                    amount: amount,
                    transaction_id: transactionId,
                    payer_id: payerInfo.payer_id,
                    payment_method: 'paypal', // Hardcode 'paypal' as the method
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Payment details recorded on server');
                    //  You can update the UI here if needed (e.g., show a success message)
                } else {
                    console.error('Failed to record payment details:', data.message);
                    //  Show an error message to the user
                }
            })
            .catch(error => {
                console.error('Error sending payment details:', error);
                //  Show an error message to the user
            });
    }
    </script>