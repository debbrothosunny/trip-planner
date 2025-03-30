<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Expense</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head> 

<body>

    <div class="container mt-5">
        <h1 class="mb-4">Create New Expense</h1>

        <!-- Form to add a new expense -->
        <form action="/user/expense/store" method="POST">

            <!-- Trip ID -->
            <div class="mb-3">
                <label for="trip_id" class="form-label">Trip</label>
                <select class="form-select" id="trip_id" name="trip_id" required>
                    <option value="" disabled selected>Select Trip</option>
                    <?php foreach ($trips as $trip): ?>
                    <option value="<?php echo htmlspecialchars($trip['id']); ?>">
                        <?php echo htmlspecialchars($trip['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-control" id="category" name="category" required>
                    <option value="Accommodation">Accommodation</option>
                    <option value="Food">Food</option>
                    <option value="Transport">Transport</option>
                    <option value="Activities">Activities</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Amount -->
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
            </div>

            <!-- Currency -->
            <div class="mb-3">
                <label for="currency" class="form-label">Currency</label>
                <select class="form-control" id="currency" name="currency" required>
                    <option value="USD">USD</option>
                </select>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>

            <!-- Expense Date -->
            <div class="mb-3">
                <label for="expense_date" class="form-label">Expense Date</label>
                <input type="date" class="form-control" id="expense_date" name="expense_date" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Save Expense</button>

        </form>

        <div class="mt-4">
            <a href="/user/expense" class="btn btn-secondary">Back to Expenses List</a>
        </div>
    </div>

    <!-- Bootstrap 5 JS (Optional for functionality like modal, dropdown, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
