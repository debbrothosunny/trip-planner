<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Hotel Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Create New Hotel Room</h1>

        <form action="/admin/hotel-room/store" method="post">
            <div class="mb-3">
                <label for="room_type_id" class="form-label">Room Type:</label>
                <select class="form-select" id="room_type_id" name="room_type_id" required>
                    <option value="">Select Room Type</option>
                    <?php if (!empty($roomTypes)): ?>
                        <?php foreach ($roomTypes as $roomType): ?>
                            <option value="<?php echo htmlspecialchars($roomType['id']); ?>"><?php echo htmlspecialchars($roomType['name']); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No active room types found</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity:</label>
                <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
            </div>
            <div class="mb-3">
                <label for="hotel_id" class="form-label">Hotel:</label>
                <select class="form-select" id="hotel_id" name="hotel_id" required>
                    <option value="">Select Hotel</option>
                    <?php if (!empty($hotels)): ?>
                        <?php foreach ($hotels as $hotel): ?>
                            <option value="<?php echo htmlspecialchars($hotel['id']); ?>"><?php echo htmlspecialchars($hotel['name']); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No hotels found</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="available_rooms" class="form-label">Available Rooms:</label>
                <input type="number" class="form-control" id="available_rooms" name="available_rooms" min="0" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Status:</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="active" value="0" checked>
                    <label class="form-check-label" for="active">
                        Active
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="pending" value="1">
                    <label class="form-check-label" for="pending">
                        Pending
                    </label>
                </div>
            </div>
            <div class="mb-3">
                <label for="total_rooms" class="form-label">Total Rooms:</label>
                <input type="number" class="form-control" id="total_rooms" name="total_rooms" min="1" required>
            </div>
            <div class="mb-3">
                <label for="amenities" class="form-label">Amenities (comma-separated):</label>
                <textarea class="form-control" id="amenities" name="amenities" rows="2"></textarea>
            </div>

            <button type="submit" class="btn btn-success">Save Hotel Room</button>
            <a href="/admin/hotel-room" class="btn btn-secondary">Back to Hotel Rooms</a>
            <a href="/admin/dashboard" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>