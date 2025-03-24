<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Hotel Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>Add New Hotel Room</h4>
            </div>
            <div class="card-body">
                <form action="/admin/hotels/rooms/store" method="POST">
                    <div class="mb-3">
                        <label for="hotel_id" class="form-label">Hotel Name</label>
                        <select name="hotel_id" id="hotel_id" class="form-control" required>
                            <option value="" disabled selected>Select Hotel</option>
                            <?php foreach ($hotels as $hotel) : ?>
                            <option value="<?= $hotel['id'] ?>"><?= htmlspecialchars($hotel['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="room_type" class="form-label">Room Type</label>
                        <input type="text" name="room_type" id="room_type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_rooms" class="form-label">Total Rooms</label>
                        <input type="number" name="total_rooms" id="total_rooms" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="available_rooms" class="form-label">Available Rooms</label>
                        <input type="number" name="available_rooms" id="available_rooms" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/admin/hotels/rooms" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Save Room
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>