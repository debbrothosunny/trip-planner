<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Itinerary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Edit Itinerary</h4>
            </div>
            <div class="card-body">
            <form action="/trip/<?= htmlspecialchars($trip_id) ?>/itinerary/<?= htmlspecialchars($data['id']) ?>/update" method="POST">

                    <input type="hidden" name="id" value="<?= htmlspecialchars($data['id'] ?? '') ?>">
                    <input type="hidden" name="trip_id" value="<?= htmlspecialchars($_GET['trip_id'] ?? '') ?>">

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="day_title" value="<?= htmlspecialchars($data['day_title'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" required><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($data['location'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="itinerary_date" value="<?= htmlspecialchars($data['itinerary_date'] ?? '') ?>" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">Update Itinerary</button>
                        <a href="/user/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
