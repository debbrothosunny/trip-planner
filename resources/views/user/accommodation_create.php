<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New Accommodation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Trip Planner</a>
        </div>
    </nav>

    <!-- Page Container -->
    <div class="container mt-5">
        <!-- Error Message Section -->
        <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Add New Accommodation</h4>
            </div>
            <div class="card-body">
                <form action="/user/accommodation/store" method="POST">

                    <!-- Location Dropdown -->
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <select class="form-select" id="location" name="location" required>
                            <option value="" disabled selected>Select Location</option>
                            <?php foreach ($locations as $loc): ?>
                            <option value="<?= htmlspecialchars($loc['location']) ?>">
                                <?= htmlspecialchars($loc['location']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Trip Dropdown -->
                    <div class="mb-3">
                        <label for="trip" class="form-label">Trip</label>
                        <select class="form-select" id="trip_id" name="trip_id" required>
                            <option value="" disabled selected>Select Trip</option>
                            <?php foreach ($trips as $trip): ?>
                            <option value="<?php echo $trip['id']; ?>"><?php echo $trip['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Hotel Dropdown -->
                    <div class="mb-3" id="hotel-container" style="display: none;">
                        <label for="hotel" class="form-label">Hotel</label>
                        <select class="form-select" id="hotel" name="hotel_id" required>
                            <option value="" disabled selected>Select Hotel</option>
                        </select>
                    </div>

                    <!-- Room Details Section -->
                    <div id="room-details-container" style="display: none;">
                        <h5>Select a Room</h5>
                        <div id="room-details" class="row"></div>

                        <!-- Hidden Inputs to Submit Room Info -->
                        <input type="hidden" id="selected_room_type" name="room_type">
                        <input type="hidden" id="selected_price" name="price">
                        <input type="hidden" id="selected_total_rooms" name="total_rooms">
                        <input type="hidden" id="selected_available_rooms" name="available_rooms">
                        <input type="hidden" id="selected_description" name="description">
                    </div>

                    <!-- Date Pickers -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="check_in_date" class="form-label">Check-in Date</label>
                            <input type="date" class="form-control" id="check_in_date" name="check_in_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="check_out_date" class="form-label">Check-out Date</label>
                            <input type="date" class="form-control" id="check_out_date" name="check_out_date" required>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Submit Accommodation</button>
                    </div>

                </form>
                <div class="text-center mt-3">
                    <a href="/user/accommodation" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dynamic JS for location & hotel loading -->
    <script>
    // Handle location change
    document.getElementById('location').addEventListener('change', function() {
        var location = this.value.trim();
        if (location) {
            document.getElementById('hotel-container').style.display = 'block';

            fetch(`/user/accommodation/fetch-hotels/${encodeURIComponent(location)}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    var hotelSelect = document.getElementById('hotel');
                    hotelSelect.innerHTML = '<option value="" disabled selected>Select Hotel</option>';

                    data.forEach(hotel => {
                        var option = document.createElement('option');
                        option.value = hotel.hotel_id;
                        option.textContent = hotel.hotel_name;
                        hotelSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching hotels:', error);
                });
        } else {
            document.getElementById('hotel-container').style.display = 'none';
        }
    });

    // Handle hotel change
    document.getElementById('hotel').addEventListener('change', function() {
        var hotelId = this.value.trim();
        if (hotelId) {
            document.getElementById('room-details-container').style.display = 'block';
            document.getElementById('room-details').innerHTML = '';

            fetch(`/user/accommodation/fetch-hotel-rooms/${hotelId}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    const container = document.getElementById('room-details');
                    if (data && data.length > 0) {
                        data.forEach((room, index) => {
                            const roomCard = document.createElement('div');
                            roomCard.classList.add('col-md-4', 'mb-3');
                            roomCard.innerHTML = `
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">${room.room_type}</h5>
                                        <p class="card-text"><strong>Price Per Day:</strong> ${room.price} $</p>
                                        <p class="card-text"><strong>Total Rooms:</strong> ${room.total_rooms}</p>
                                        <p class="card-text"><strong>Available Rooms:</strong> ${room.available_rooms}</p>
                                        <p class="card-text"><strong>Description:</strong> ${room.description}</p>
                                        <button type="button" class="btn btn-primary select-room-btn" data-index="${index}" data-room-id="${room.id}">
                                            Select Room
                                        </button>
                                    </div>
                                </div>
                            `;
                            container.appendChild(roomCard);
                        });
                    } else {
                        container.innerHTML = '<p>No rooms available for this hotel.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching room details:', error);
                    document.getElementById('room-details').innerHTML =
                        '<p class="text-danger">Error loading room details. Try again later.</p>';
                });
        } else {
            document.getElementById('room-details-container').style.display = 'none';
        }
    });

    document.getElementById('room-details').addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('select-room-btn')) {
            const roomCard = e.target.closest('.card-body');

            // Get room details
            const roomType = roomCard.querySelector('.card-title').innerText; // This is the room type
            const priceText = roomCard.querySelectorAll('.card-text')[0].innerText;
            const totalRoomsText = roomCard.querySelectorAll('.card-text')[1].innerText;
            const availableRoomsText = roomCard.querySelectorAll('.card-text')[2].innerText;
            const descriptionText = roomCard.querySelectorAll('.card-text')[3].innerText;

            // Extract values
            const price = priceText.split(':')[1].replace('$', '').trim();
            const totalRooms = totalRoomsText.split(':')[1].trim();
            const availableRooms = availableRoomsText.split(':')[1].trim();
            const description = descriptionText.split(':')[1].trim();

            // Set the hidden fields with the room type (and other room details if needed)
            document.getElementById('selected_room_type').value = roomType; // Store room type here
            document.getElementById('selected_price').value = price;
            document.getElementById('selected_total_rooms').value = totalRooms;
            document.getElementById('selected_available_rooms').value = availableRooms;
            document.getElementById('selected_description').value = description;

            alert(`Room "${roomType}" Selected!`);
        }
    });
    </script>

</body>

</html>