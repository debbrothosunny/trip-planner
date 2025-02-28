<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Accommodation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">
        <h2 class="text-center mb-4">Add New Accommodation</h2>


        <form action="/user/accommodation/store" method="POST">
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

            <div class="mb-3">
                <label class="form-label" for="name">Hotel Name</label>
                <input type="text" name="name" id="name" class="form-control" required
                    placeholder="Enter accommodation name">
            </div>

            <div class="mb-3">
                <label class="form-label" for="location">Location</label>
                <input type="text" name="location" id="location" class="form-control" required
                    placeholder="Enter location">
            </div>

            <div class="mb-3">
                <label class="form-label" for="price">Price</label>
                <input type="number" name="price" id="price" class="form-control" required placeholder="Enter price">
            </div>

            <div class="mb-3">
                <label class="form-label" for="amenities">Amenities</label>
                <textarea name="amenities" id="amenities" class="form-control"
                    placeholder="Enter amenities (optional)"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label" for="check_in_time">Check-in Time</label>
                <input type="time" name="check_in_time" id="check_in_time" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="check_out_time">Check-out Time</label>
                <input type="time" name="check_out_time" id="check_out_time" class="form-control" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="/user/accommodation" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Save Accommodation</button>
            </div>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>