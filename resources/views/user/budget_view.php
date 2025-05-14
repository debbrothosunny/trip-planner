<?php
$header_title = "Budget View";
include __DIR__ . '/../backend/layouts/app.php';
?>

<style>
    

    .content {
        margin-left: 270px;
        padding: 20px;
        width: 100%;
    }

    .trip-card-details {
        margin-top: 10px;
        border-top: 1px solid #eee;
        padding-top: 10px;
    }
</style>

<div class="container mt-5" id="budget-view-app">
    <?php if (!empty($tripExpensesData)): ?>
    <div class="row">
        <?php foreach ($tripExpensesData as $tripData): ?>
        <div class="col-md-4 mb-4">
            <?php if ($tripData['totalOverall'] > $tripData['budget']): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Warning!</strong> The total expenses exceed the allocated budget.
            </div>
            <?php elseif ($tripData['totalOverall'] < $tripData['budget']): ?>
            <div class="alert alert-success" role="alert">
                <strong>Good!</strong> You are within the budget, and there's room for more expenses.
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title"><?php echo htmlspecialchars($tripData['trip_name']); ?></h5>
                </div>
                <div class="card-body">
                    <button @click="toggleDetails('<?php echo htmlspecialchars($tripData['trip_name']); ?>')" class="btn btn-sm btn-outline-info mb-2">
                        {{ showDetails[ '<?php echo htmlspecialchars($tripData['trip_name']); ?>' ] ? 'Hide Details' : 'Show Details' }}
                    </button>
                    <div v-if="showDetails[ '<?php echo htmlspecialchars($tripData['trip_name']); ?>' ]" class="trip-card-details">
                        <p class="card-text"><strong> Accommodation:</strong> $<?php echo number_format($tripData['totalAccommodation'], 2); ?></p>
                        <p class="card-text"><strong> Transportation:</strong> $<?php echo number_format($tripData['totalTransportation'], 2); ?></p>
                        <p class="card-text"><strong> Expenses:</strong> $<?php echo number_format($tripData['totalExpenses'], 2); ?></p>
                    </div>
                    <p class="card-text"><strong> Overall:</strong> $<?php echo number_format($tripData['totalOverall'], 2); ?></p>
                </div>
                <div class="card-footer text-muted">
                    <strong>Budget:</strong> $<?php echo number_format($tripData['budget'], 2); ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-warning" role="alert">
        No trips found for this user.
    </div>
    <?php endif; ?>
</div>



<script>
    const { createApp, ref } = Vue;

    createApp({
        data() {
            return {
                showDetails: {} // Object to track visibility of details for each trip
            };
        },
        methods: {
            toggleDetails(tripName) {
                this.showDetails[tripName] = !this.showDetails[tripName];
            }
        }
    }).mount('#budget-view-app');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>