<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Transportation</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">
        <h1 class="mb-4">Add New Transportation</h1>

        <form action="/user/transportation/store" method="POST">
            <div class="mb-3">
                <label for="trip_id" class="form-label">Trip Name</label>
                <select class="form-select" id="trip_id" name="trip_id" required>
                    <option value="" disabled selected>Select Trip</option>
                    <?php foreach ($trips as $trip): ?>
                    <option value="<?php echo $trip['id']; ?>"><?php echo $trip['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Transportation Type</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="" disabled selected>Select Type</option>
                    <option value="Bus">Bus</option>
                    <option value="Train">Train</option>
                    <option value="Flight">Flight</option>
                    <option value="Ship">Ship</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>

            <div class="mb-3">
                <label for="departure_location" class="form-label">Departure Location</label>
                <input type="text" class="form-control" id="departure_location" name="departure_location" required>
            </div>

            <div class="mb-3">
                <label for="arrival_location" class="form-label">Arrival Location</label>
                <input type="text" class="form-control" id="arrival_location" name="arrival_location" required>
            </div>

            <div class="mb-3">
                <label for="departure_date" class="form-label">Departure Date</label>
                <input type="date" class="form-control" id="departure_date" name="departure_date" required>
            </div>

            <div class="mb-3">
                <label for="arrival_date" class="form-label">Arrival Date</label>
                <input type="date" class="form-control" id="arrival_date" name="arrival_date" required>
            </div>

            <div class="mb-3">
                <label for="booking_reference" class="form-label">Booking Reference</label>
                <input type="text" class="form-control" id="booking_reference" name="booking_reference" required>
            </div>

            <!-- New Mount Field -->
            <div class="mb-3">
                <label for="amount" class="form-label">AMount</label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
            </div>

            <button type="submit" class="btn btn-success">Add Transportation</button>
            <a href="/user/transportation" class="btn btn-secondary">Cancel</a>
        </form>

    </div>



</body>

</html>