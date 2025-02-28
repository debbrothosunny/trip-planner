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

        <?php if (isset($_GET['error']) && $_GET['error'] == 'update_failed'): ?>
        <div class="alert alert-danger">Failed to update the accommodation. Please try again.</div>
        <?php endif; ?>

        <form action="/user/accommodation/update/<?= htmlspecialchars($accommodation['id']); ?>" method="POST">

            <div class="mb-3">
                <label for="trip_id" class="form-label">Trip Name</label>
                <select class="form-select" id="trip_id" name="trip_id" required>
                    <option value="" disabled>Select Trip</option>
                    <?php foreach ($trips as $trip): ?>
                    <option value="<?php echo $trip['id']; ?>"
                        <?php echo ($trip['id'] == $accommodation['trip_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($trip['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Hotel Name</label>
                <input type="text" class="form-control" name="name"
                    value="<?= htmlspecialchars($accommodation['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" name="location"
                    value="<?= htmlspecialchars($accommodation['location']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" name="price"
                    value="<?= htmlspecialchars($accommodation['price']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="amenities" class="form-label">Amenities</label>
                <input type="text" class="form-control" name="amenities"
                    value="<?= htmlspecialchars($accommodation['amenities']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="check_in_time" class="form-label">Check-in Time</label>
                <input type="time" class="form-control" name="check_in_time"
                    value="<?= htmlspecialchars($accommodation['check_in_time']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="check_out_time" class="form-label">Check-out Time</label>
                <input type="time" class="form-control" name="check_out_time"
                    value="<?= htmlspecialchars($accommodation['check_out_time']); ?>" required>
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