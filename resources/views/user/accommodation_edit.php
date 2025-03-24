<?php
// Assuming $accommodation contains the data passed from the controller
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Accommodation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Accommodation</h2>

        <?php if (isset($_SESSION['sweetalert'])): ?>
        <div class="alert alert-<?php echo $_SESSION['sweetalert']['icon'] == 'error' ? 'danger' : 'success'; ?>">
            <?php echo $_SESSION['sweetalert']['text']; ?>
        </div>
        <?php unset($_SESSION['sweetalert']); endif; ?>

        <form action="/user/accommodation/update/<?= htmlspecialchars($accommodation['id']); ?>" method="POST">

            <div class="mb-3">
                <label for="check_in_date" class="form-label">Check-in Date</label>
                <input type="date" class="form-control" name="check_in_date" value="<?= htmlspecialchars($accommodation['check_in_date']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="check_out_date" class="form-label">Check-out Date</label>
                <input type="date" class="form-control" name="check_out_date" value="<?= htmlspecialchars($accommodation['check_out_date']); ?>" required>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Update Accommodation</button>
                <a href="/user/accommodation" class="btn btn-secondary">Back to List</a>
            </div>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
