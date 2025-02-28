<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transportation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        max-width: 600px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
    }

    .btn-custom {
        width: 100%;
    }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="text-center mb-4">✏️ Edit Transportation</h2>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <form action="/user/transportation/update/<?php echo $transportation['id']; ?>" method="POST">
            <div class="mb-3">
                <label for="trip_id" class="form-label">Trip Name</label>
                <select class="form-select" id="trip_id" name="trip_id" required>
                    <option value="" disabled>Select Trip</option>
                    <?php foreach ($trips as $trip): ?>
                    <option value="<?php echo $trip['id']; ?>"
                        <?php echo ($trip['id'] == $transportation['trip_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($trip['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Transportation Type</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="" disabled>Select Transportation Type</option>
                    <?php 
            $transportationTypes = ['Bus', 'Train', 'Flight', 'Ship'];
            foreach ($transportationTypes as $type): ?>
                    <option value="<?php echo $type; ?>"
                        <?php echo ($type == $transportation['type']) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($type); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name"
                    value="<?php echo $transportation['company_name']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="departure_location" class="form-label">Departure Location</label>
                <input type="text" class="form-control" id="departure_location" name="departure_location"
                    value="<?php echo $transportation['departure_location']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="arrival_location" class="form-label">Arrival Location</label>
                <input type="text" class="form-control" id="arrival_location" name="arrival_location"
                    value="<?php echo $transportation['arrival_location']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="departure_date" class="form-label">Departure Date</label>
                <input type="date" class="form-control" id="departure_date" name="departure_date"
                    value="<?php echo date('Y-m-d', strtotime($transportation['departure_date'])); ?>" required>
            </div>

            <div class="mb-3">
                <label for="arrival_date" class="form-label">Arrival Date</label>
                <input type="date" class="form-control" id="arrival_date" name="arrival_date"
                    value="<?php echo date('Y-m-d', strtotime($transportation['arrival_date'])); ?>" required>
            </div>

            <div class="mb-3">
                <label for="booking_reference" class="form-label">Booking Reference</label>
                <input type="text" class="form-control" id="booking_reference" name="booking_reference"
                    value="<?php echo $transportation['booking_reference']; ?>" required>
            </div>

            <!-- New Mount Field -->
            <div class="mb-3">
                <label for="mount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0"
                    value="<?php echo isset($transportation['amount']) ? $transportation['amount'] : ''; ?>" required>
            </div>

            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user']['id']; ?>">

            <button type="submit" class="btn btn-primary btn-custom">Update Transportation</button>
        </form>

        <div class="text-center mt-3">
            <a href="/user/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>