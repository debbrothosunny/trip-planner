<?php
$header_title = "Trip Accommodation";
include __DIR__ . '/../backend/layouts/app.php';

?>
<div class="container">
    <h4 class="mt-5 text-center">Your Accommodations</h4>

    <?php if (empty($accommodations)): ?>
    <div class="alert alert-info" role="alert">
        You haven't booked any accommodations yet.
    </div>
    <?php endif; ?>

    <a href="/user/accommodation/create" class="btn btn-primary mb-3">Book New Accommodation</a>

    <?php if (!empty($accommodations)): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($accommodations as $item): ?>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Booking ID: <?php echo htmlspecialchars($item['id']); ?></h5>
                    <p class="card-text"><strong>Hotel:</strong> <?php echo htmlspecialchars($item['hotel_name']); ?></p>
                    <p class="card-text"><strong>Room Type:</strong> <?php echo htmlspecialchars($item['room_type_name']); ?></p>
                    <p class="card-text">
                        <strong>Check-in:</strong>
                        <?php echo htmlspecialchars(date('j F, Y H:i', strtotime($item['check_in_date']))); ?>
                    </p>
                    <p class="card-text">
                        <strong>Check-out:</strong>
                        <?php echo htmlspecialchars(date('j F, Y H:i', strtotime($item['check_out_date']))); ?>
                    </p>
                    <p class="card-text">
                        <strong>Status:</strong>
                        <?php
                        $statusClass = '';
                        if (isset($item['status'])) {
                            switch ($item['status']) {
                                case 0:
                                    $statusClass = 'badge bg-success';
                                    echo '<span class="' . $statusClass . '">Confirmed</span>';
                                    break;
                                case 1:
                                    $statusClass = 'badge bg-warning';
                                    echo '<span class="' . $statusClass . '">Pending</span>';
                                    break;
                                default:
                                    echo htmlspecialchars(ucfirst($item['status']));
                                    break;
                            }
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </p>
                    <p class="card-text">
                        <strong>Price Per Day:</strong>
                        <?php if (isset($item['room_price'])): ?>
                        <?php $pricePerDay = floatval($item['room_price']); ?>
                        <?php echo htmlspecialchars(number_format($pricePerDay, 2)); ?> USD
                        <?php else: ?>
                        N/A
                        <?php endif; ?>
                    </p>
                    <p class="card-text">
                        <strong>Estimated Total Price:</strong>
                        <?php if (isset($item['check_in_date']) && isset($item['check_out_date']) && isset($item['room_price'])): ?>
                        <?php
                            $checkIn = new DateTime($item['check_in_date']);
                            $checkOut = new DateTime($item['check_out_date']);
                            $interval = $checkIn->diff($checkOut);
                            $numberOfNights = $interval->days;
                            $estimatedTotalPrice = floatval($item['room_price']) * $numberOfNights;
                            echo htmlspecialchars(number_format($estimatedTotalPrice, 2)); ?> USD
                        <?php else: ?>
                        N/A
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($item['room_description'])): ?>
                    <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($item['room_description']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($item['room_amenities'])): ?>
                    <p class="card-text">
                        <strong>Amenities:</strong>
                        <ul class="amenities-list">
                            <?php
                                $amenities = json_decode($item['room_amenities'], true);
                                if (is_array($amenities)):
                                    foreach ($amenities as $amenity): ?>
                            <li><?php echo htmlspecialchars($amenity); ?></li>
                            <?php endforeach;
                                else: ?>
                            <li><?php echo htmlspecialchars($item['room_amenities']); ?></li>
                            <?php endif; ?>
                        </ul>
                    </p>
                    <?php endif; ?>

                    <div class="payment-button-container">
                        <?php if (isset($item['payment_status'])): ?>
                        <?php if ($item['payment_status'] === 'pending'): ?>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#paypalModal-<?php echo htmlspecialchars($item['id']); ?>">
                            Pay with PayPal
                        </button>
                        <?php elseif ($item['payment_status'] === 'completed'): ?>
                        <span class="badge bg-success">Paid</span>
                        <?php elseif ($item['payment_status'] === 'failed'): ?>
                        <span class="badge bg-danger">Payment Failed</span>
                        <button type="button" class="btn btn-warning btn-sm ms-2" data-bs-toggle="modal"
                            data-bs-target="#paypalModal-<?php echo htmlspecialchars($item['id']); ?>">
                            Retry Payment
                        </button>
                        <?php else: ?>
                        <span
                            class="badge bg-secondary"><?php echo htmlspecialchars(ucfirst($item['payment_status'])); ?></span>
                        <?php endif; ?>
                        <?php else: ?>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#paypalModal-<?php echo htmlspecialchars($item['id']); ?>">
                            Pay with PayPal
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal fade" id="paypalModal-<?php echo htmlspecialchars($item['id']); ?>" tabindex="-1"
                    aria-labelledby="paypalModalLabel-<?php echo htmlspecialchars($item['id']); ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"
                                    id="paypalModalLabel-<?php echo htmlspecialchars($item['id']); ?>">
                                    Pay with PayPal - Booking ID: <?php echo htmlspecialchars($item['id']); ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>You are about to pay <strong><?php
                                                                    if (isset($item['check_in_date']) && isset($item['check_out_date']) && isset($item['room_price'])):
                                                                        $checkIn = new DateTime($item['check_in_date']);
                                                                        $checkOut = new DateTime($item['check_out_date']);
                                                                        $interval = $checkIn->diff($checkOut);
                                                                        $numberOfNights = $interval->days;
                                                                        $estimatedTotalPrice = floatval($item['room_price']) * $numberOfNights;
                                                                        echo htmlspecialchars(number_format($estimatedTotalPrice, 2));
                                                                    else:
                                                                        echo 'N/A';
                                                                    endif;
                                                                    ?> USD</strong> for booking ID
                                    <strong><?php echo htmlspecialchars($item['id']); ?></strong>.
                                </p>
                                <p>Click the button below to proceed to PayPal.</p>
                                <form action="/user/accommodation/payment/initiate" method="post">
                                    <input type="hidden" name="accommodation_id"
                                        value="<?php echo htmlspecialchars($item['id']); ?>">
                                    <input type="hidden" name="amount" value="<?php
                                                                                if (isset($item['check_in_date']) && isset($item['check_out_date']) && isset($item['room_price'])):
                                                                                    $checkIn = new DateTime($item['check_in_date']);
                                                                                    $checkOut = new DateTime($item['check_out_date']);
                                                                                    $interval = $checkIn->diff($checkOut);
                                                                                    $numberOfNights = $interval->days;
                                                                                    $estimatedTotalPrice = floatval($item['room_price']) * $numberOfNights;
                                                                                    echo htmlspecialchars(number_format($estimatedTotalPrice, 2));
                                                                                endif;
                                                                                ?>">
                                    <input type="hidden" name="trip_id"
                                        value="<?php echo htmlspecialchars($item['trip_id']); ?>">
                                    <button type="submit" class="btn btn-primary">Proceed to PayPal</button>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.row-cols-1 > * {
  padding-left: 0 !important;
  padding-right: 0 !important;
}
.row-cols-md-2 > * {
  padding-left: 0 !important;
  padding-right: 0 !important;
}
.row-cols-lg-3 > * {
  padding-left: 0 !important;
  padding-right: 0 !important;
}

.card {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    margin-bottom: 1.5rem;
}

.card-body {
    padding: 1.25rem;
}

.card-title {
    margin-bottom: 0.75rem;
    font-size: 1.25rem;
}

.card-text {
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.badge {
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.35em 0.65em;
    border-radius: 0.25rem;
}

.bg-success {
    background-color: #198754 !important;
    color: white !important;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: black !important;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: white !important;
}

.bg-secondary {
    background-color: #6c757d !important;
    color: white !important;
}

.amenities-list {
    padding-left: 15px;
    margin-bottom: 0;
}

.amenities-list li {
    list-style-type: disc;
    font-size: 1rem;
}

.payment-button-container {
    margin-top: 1rem;
}
</style>