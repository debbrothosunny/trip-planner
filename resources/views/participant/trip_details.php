<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trip Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease-in-out;
    }

    .card:hover {
        transform: scale(1.02);
    }

    .card-header {
        font-weight: bold;
    }

    .amount {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: bold;
        padding: 10px 0;
    }
    </style>
</head>

<body>

    <div class="container my-5">
        <h2 class="text-center mb-4">🌍 Trip Details</h2>

        <div class="row g-4">
            <!-- Itinerary Section -->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">📅 Itinerary</div>
                    <div class="card-body">
                        <?php if (!empty($itinerary)) : ?>
                        <?php foreach ($itinerary as $item): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <h5 class="mb-1"><?= htmlspecialchars($item['day_title']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($item['description']) ?></p>
                            <small class="text-secondary"><?= htmlspecialchars($item['itinerary_date']) ?></small>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p class="text-muted">No itinerary available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Accommodations Section -->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">🏨 Accommodations</div>
                    <div class="card-body">
                        <?php if (!empty($accommodations)) : ?>
                        <?php foreach ($accommodations as $accommodation): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <h5 class="mb-1"><?= htmlspecialchars($accommodation['name']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($accommodation['location']) ?></p>
                            <small>Check-in: <?= htmlspecialchars($accommodation['check_in_time']) ?> | Check-out:
                                <?= htmlspecialchars($accommodation['check_out_time']) ?></small>
                            <div class="amount text-success">$<?= htmlspecialchars($accommodation['price']) ?></div>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p class="text-muted">No accommodations available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Transportation Section -->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">🚗 Transportation</div>
                    <div class="card-body">
                        <?php if (!empty($transportation)) : ?>
                        <?php foreach ($transportation as $transport): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <h5 class="mb-1"><?= htmlspecialchars($transport['type']) ?></h5>
                            <p class="text-muted">Departure: <?= htmlspecialchars($transport['departure_location']) ?> |
                                Arrival: <?= htmlspecialchars($transport['arrival_location']) ?> | Comapny Name:
                                <?= htmlspecialchars($transport['company_name']) ?>| Departure Date:
                                <?= htmlspecialchars($transport['departure_date']) ?>| Arrival Date:
                                <?= htmlspecialchars($transport['arrival_date']) ?>| Amount:
                                $<?= htmlspecialchars($transport['amount']) ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p class="text-muted">No transportation details available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Expenses Section -->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">💰 Expenses</div>
                    <div class="card-body">
                        <?php if (!empty($expenses)) : ?>
                        <?php foreach ($expenses as $expense): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <h5 class="mb-1"><?= htmlspecialchars($expense['description']) ?></h5>
                            <p class="text-muted">Date: <?= htmlspecialchars($expense['expense_date']) ?></p>
                            <div class="amount text-danger">$<?= htmlspecialchars($expense['amount']) ?></div>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p class="text-muted">No expenses recorded.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>  
        </div>
        <div class="text-center mt-3">
           <a href="/participant/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>
    </div>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>