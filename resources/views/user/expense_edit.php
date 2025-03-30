<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="container mt-5">
        <h1 class="mb-4">Edit Expense</h1>

        <form action="/user/expense/update/<?php echo htmlspecialchars($expense['id']); ?>" method="POST">
            <input type="hidden" name="expense_id" value="<?php echo htmlspecialchars($expense['id']); ?>">

            <!-- Trip ID -->  
            <div class="mb-3">
                <label for="trip_id" class="form-label">Trip</label>
                <select class="form-select" id="trip_id" name="trip_id" required>
                    <?php foreach ($trips as $trip): ?>
                    <option value="<?php echo htmlspecialchars($trip['id']); ?>"
                        <?php echo $trip['id'] == $expense['trip_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($trip['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-control" id="category" name="category" required>
                    <option value="Accommodation"
                        <?php echo ($expense['category'] == 'Accommodation') ? 'selected' : ''; ?>>Accommodation
                    </option>
                    <option value="Food" <?php echo ($expense['category'] == 'Food') ? 'selected' : ''; ?>>Food</option>
                    <option value="Transport" <?php echo ($expense['category'] == 'Transport') ? 'selected' : ''; ?>>
                        Transport</option>
                    <option value="Activities" <?php echo ($expense['category'] == 'Activities') ? 'selected' : ''; ?>>
                        Activities</option>
                    <option value="Other" <?php echo ($expense['category'] == 'Other') ? 'selected' : ''; ?>>Other
                    </option>
                </select>
            </div>

            <!-- Amount -->
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01"
                    value="<?php echo htmlspecialchars($expense['amount']); ?>" required>
            </div>

            <!-- Currency -->
            <div class="mb-3">
                <label for="currency" class="form-label">Currency</label>
                <select class="form-control" id="currency" name="currency" required>
                    <option value="USD" <?php echo ($expense['currency'] == 'USD') ? 'selected' : ''; ?>>USD</option>
                </select>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description"
                    name="description"><?php echo htmlspecialchars($expense['description']); ?></textarea>
            </div>

            <!-- Expense Date -->
            <div class="mb-3">
                <label for="expense_date" class="form-label">Expense Date</label>
                <input type="date" class="form-control" id="expense_date" name="expense_date"
                    value="<?php echo htmlspecialchars($expense['expense_date']); ?>" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-success">Update Expense</button>
            <a href="/user/expense" class="btn btn-secondary">Cancel</a>

        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
   

    <?php
// Ensure session is only started if it's not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SweetAlert Handling
if (isset($_SESSION['success'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: '{$_SESSION['success']}',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                Optional: Redirect after success alert closes (if needed)
                window.location.href = '/user/expense'; // Uncomment if you want to redirect
            });
        });
    </script>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error!',
                text: '{$_SESSION['error']}',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                Optional: Redirect after error alert closes (if needed)
                window.location.href = '/user/expense'; // Uncomment if you want to redirect
            });
        });
    </script>";
    unset($_SESSION['error']);
}
?>

</body>

</html>