<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Create New Hotel</h2>
        <form action="/admin/hotel/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <div class="mb-3">
                <label for="country_id" class="form-label">Country:</label>
                <select class="form-select" id="country_id" name="country_id" required>
                    <option value="">Select Country</option>
                    <?php if (!empty($countries)): ?>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo htmlspecialchars($country['id']); ?>"><?php echo htmlspecialchars($country['name']); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No active countries found</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="state_id" class="form-label">State:</label>
                <select class="form-select" id="state_id" name="state_id" required>
                    <option value="">Select State</option>
                    <?php if (!empty($states)): ?>
                        <?php foreach ($states as $state): ?>
                            <option value="<?php echo htmlspecialchars($state['id']); ?>"><?php echo htmlspecialchars($state['name']); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No active states found</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address:</label>
                <textarea class="form-control" id="address" name="address"></textarea>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="mb-3">
                <label for="star_rating" class="form-label">Star Rating:</label>
                <select class="form-select" id="star_rating" name="star_rating" required>
                    <option value="">Select Rating</option>
                    <option value="1">1 Star</option>
                    <option value="2">2 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="5">5 Stars</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="0">Active</option>
                    <option value="1">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create Hotel</button>
            <a href="/admin/hotel" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>  