<?php
$header_title = "Budget View";
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



<!-- Container for the page content -->
<div class="container mt-5">
    <!-- Check if there are trip expenses data -->
    <?php if (!empty($tripExpensesData)): ?>
    <div class="row">
        <!-- Loop through each trip's budget data -->
        <?php foreach ($tripExpensesData as $tripData): ?>
        <div class="col-md-4 mb-4">
            <!-- Check if the total overall exceeds the budget -->
            <?php if ($tripData['totalOverall'] > $tripData['budget']): ?>
            <!-- Alert for budget exceeding -->
            <div class="alert alert-danger" role="alert">
                <strong>Warning!</strong> The total expenses exceed the allocated budget.
            </div>
            <?php elseif ($tripData['totalOverall'] < $tripData['budget']): ?>
            <!-- Alert for budget not fully used -->
            <div class="alert alert-success" role="alert">
                <strong>Good!</strong> You are within the budget, and there's room for more expenses.
            </div>
            <?php endif; ?>

            <!-- Card for each trip -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title"><?php echo htmlspecialchars($tripData['trip_name']); ?></h5>
                </div>
                <div class="card-body">
                    <p class="card-text"><strong> Accommodation:</strong>
                        $<?php echo number_format($tripData['totalAccommodation'], 2); ?></p>
                    <p class="card-text"><strong> Transportation:</strong>
                        $<?php echo number_format($tripData['totalTransportation'], 2); ?></p>
                    <p class="card-text"><strong> Expenses:</strong>
                        $<?php echo number_format($tripData['totalExpenses'], 2); ?></p>
                    <p class="card-text"><strong> Overall:</strong>
                        $<?php echo number_format($tripData['totalOverall'], 2); ?></p>
                </div>
                <div class="card-footer text-muted">
                    <strong>Budget:</strong> $<?php echo number_format($tripData['budget'], 2); ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <!-- Message when no trip data is available -->
    <div class="alert alert-warning" role="alert">
        No trips found for this user.
    </div>
    <?php endif; ?>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
