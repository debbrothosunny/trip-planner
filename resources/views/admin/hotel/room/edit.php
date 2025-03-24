<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hotel Room</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (Optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Container for the page -->
    <div class="container mt-5">
        <!-- Card to hold the form -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white">
                <h4>Edit Hotel Room</h4>
            </div>
            <div class="card-body">
                <!-- Form to edit room details -->
                <form action="/admin/hotels/rooms/update/<?= $room['id'] ?>" method="POST">
                    <!-- Hotel ID Selection -->
                    <div class="mb-3">
                        <label for="hotel_id" class="form-label">Hotel</label>
                        <select name="hotel_id" id="hotel_id" class="form-select" required>
                            <option value="" disabled>Select Hotel</option>
                            <!-- Loop through hotels dynamically -->
                            <?php foreach ($hotels as $hotel): ?>
                                <option value="<?= $hotel['id'] ?>" <?= $hotel['id'] == $room['hotel_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($hotel['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Room Type Input -->
                    <div class="mb-3">
                        <label for="room_type" class="form-label">Room Type</label>
                        <input type="text" name="room_type" id="room_type" class="form-control" value="<?= htmlspecialchars($room['room_type']) ?>" required>
                    </div>

                    <!-- Price Input -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" id="price" class="form-control" value="<?= $room['price'] ?>" step="0.01" required>
                    </div>

                    <!-- Total Rooms Input -->
                    <div class="mb-3">
                        <label for="total_rooms" class="form-label">Total Rooms</label>
                        <input type="number" name="total_rooms" id="total_rooms" class="form-control" value="<?= $room['total_rooms'] ?>" required>
                    </div>

                    <!-- Available Rooms Input -->
                    <div class="mb-3">
                        <label for="available_rooms" class="form-label">Available Rooms</label>
                        <input type="number" name="available_rooms" id="available_rooms" class="form-control" value="<?= $room['available_rooms'] ?>" required>
                    </div>

                    <!-- Description Textarea -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3" required><?= htmlspecialchars($room['description']) ?></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="/admin/hotels/rooms" class="btn btn-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Back
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil-square"></i> Update Room
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS (optional for functionality like modals, tooltips, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
