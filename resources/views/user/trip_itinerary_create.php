<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Itinerary</title>
    <!-- Add Bootstrap CSS link (if needed) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>  

    <div class="container mt-5">
        <h2>Create New Itinerary for Trip #<?= htmlspecialchars($trip_id) ?>Trip</h2>

        <!-- The form for creating a new itinerary -->
        <form action="/trip/<?= htmlspecialchars($trip_id) ?>/itinerary/create" method="POST">


            <input type="hidden" name="trip_id" value="<?= htmlspecialchars($trip_id) ?>">

            <div class="mb-3">
                <label for="day_title" class="form-label">Day</label>
                <input type="text" name="day_title" id="day_title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="itinerary_date" class="form-label">Date</label>
                <input type="date" name="itinerary_date" id="itinerary_date" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Itinerary</button>
        </form>
    </div>


    <!-- Add Bootstrap JS link (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>