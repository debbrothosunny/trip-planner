<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        margin-top: 30px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4">Create New Booking</h1>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger mb-3">
            <?php
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); // Clear the error message
            ?>
        </div>
        <?php endif; ?>

        <form action="/user/accommodation/store" method="POST">

            <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <select class="form-select" id="country" name="country" required>
                    <option value="">Select Country</option>
                    <?php if (isset($countries) && is_array($countries)): ?>
                    <?php foreach ($countries as $country): ?>
                    <option value="<?php echo htmlspecialchars($country['id']); ?>">
                        <?php echo htmlspecialchars($country['name']); ?></option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="state" class="form-label">State</label>
                <select class="form-select" id="state" name="state" disabled required>
                    <option value="">Select State</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="hotel_id" class="form-label">Hotel</label>
                <select class="form-select" id="hotel_id" name="hotel_id" disabled required>
                    <option value="">Select Hotel</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="room_type_id" class="form-label">Room Type</label>
                <select class="form-select" id="room_type_id" name="room_type_id" disabled required>
                    <option value="">Select Room Type</option>
                    <?php if (isset($roomTypes) && is_array($roomTypes)): ?>
                    <?php foreach ($roomTypes as $roomType): ?>
                    <option value="<?php echo htmlspecialchars($roomType['id']); ?>"
                        data-default-price="<?php echo htmlspecialchars($roomType['price'] ?? ''); ?>"
                        data-description="<?php echo htmlspecialchars($roomType['description'] ?? ''); ?>"
                        data-amenities="<?php echo htmlspecialchars($roomType['amenities'] ?? ''); ?>">
                        <?php echo htmlspecialchars($roomType['name'] ?? 'N/A'); ?>
                        (Price:
                        <?php echo isset($roomType['price']) ? htmlspecialchars(number_format($roomType['price'], 2)) : 'N/A'; ?>
                        USD)
                    </option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div id="room-details-container" class="mb-3">
                <h5>Room Details:</h5>
                <p><strong>Description:</strong> <span id="room-description"></span></p>
                <p><strong>Amenities:</strong> <span id="room-amenities"></span></p>
                <p><strong>Total Rooms:</strong> <span id="room-total"></span></p>
                <p><strong>Available Rooms:</strong> <span id="room-available"></span></p>
            </div>

            <div class="mb-3">
                <label for="check_in_date" class="form-label">Check-in Date and Time</label>
                <input type="datetime-local" class="form-control" id="check_in_date" name="check_in_date" required>
            </div>

            <div class="mb-3">
                <label for="check_out_date" class="form-label">Check-out Date and Time</label>
                <input type="datetime-local" class="form-control" id="check_out_date" name="check_out_date" required>
            </div>

            <div class="mb-3">
                <label for="trip_id" class="form-label">Select Trip</label>
                <select class="form-select" id="trip_id" name="trip_id" required>
                    <option value="">-- Select Trip --</option>
                    <?php if (isset($trips) && is_array($trips)): ?>
                    <?php foreach ($trips as $trip): ?>
                    <option value="<?php echo htmlspecialchars($trip['id']); ?>">
                        <?php echo htmlspecialchars($trip['name']); ?>
                    </option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div id="availability-message" class="mt-3"></div>

            <button type="submit" class="btn btn-primary">Book Now</button>
            <a href="/user/accommodation" class="btn btn-secondary ms-2">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const countrySelect = document.getElementById('country');
        const stateSelect = document.getElementById('state');
        const hotelSelect = document.getElementById('hotel_id');
        const roomTypeSelect = document.getElementById('room_type_id');
        const roomDetailsContainer = document.getElementById('room-details-container');
        const roomDescriptionSpan = document.getElementById('room-description');
        const roomAmenitiesSpan = document.getElementById('room-amenities');
        const roomTotalSpan = document.getElementById('room-total');
        const roomAvailableSpan = document.getElementById('room-available');
        const checkInDateInput = document.getElementById('check_in_date');
        const checkOutDateInput = document.getElementById('check_out_date');
        const availabilityMessageDiv = document.getElementById('availability-message');

        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            if (countryId) {
                fetch(`/user/accommodation/states/${countryId}`)
                    .then(response => response.json())
                    .then(states => {
                        stateSelect.innerHTML = '<option value="">Select State</option>';
                        stateSelect.disabled = false;
                        states.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.id;
                            option.textContent = state.name;
                            stateSelect.appendChild(option);
                        });
                        hotelSelect.disabled = true;
                        hotelSelect.innerHTML = '<option value="">Select Hotel</option>';
                        roomTypeSelect.disabled = true;
                        roomTypeSelect.innerHTML = '<option value="">Select Room Type</option>';
                        roomDetailsContainer.innerHTML =
                            '<h5>Room Details:</h5><p><strong>Description:</strong> <span id="room-description"></span></p><p><strong>Amenities:</strong> <span id="room-amenities"></span></p><p><strong>Total Rooms:</strong> <span id="room-total"></span></p><p><strong>Available Rooms:</strong> <span id="room-available"></span></p>';
                        roomDescriptionSpan.textContent = '';
                        roomAmenitiesSpan.textContent = '';
                        roomTotalSpan.textContent = '';
                        roomAvailableSpan.textContent = '';
                    });
            } else {
                stateSelect.disabled = true;
                stateSelect.innerHTML = '<option value="">Select State</option>';
                hotelSelect.disabled = true;
                hotelSelect.innerHTML = '<option value="">Select Hotel</option>';
                roomTypeSelect.disabled = true;
                roomTypeSelect.innerHTML = '<option value="">Select Room Type</option>';
                roomDetailsContainer.innerHTML =
                    '<h5>Room Details:</h5><p><strong>Description:</strong> <span id="room-description"></span></p><p><strong>Amenities:</strong> <span id="room-amenities"></span></p><p><strong>Total Rooms:</strong> <span id="room-total"></span></p><p><strong>Available Rooms:</strong> <span id="room-available"></span></p>';
                roomDescriptionSpan.textContent = '';
                roomAmenitiesSpan.textContent = '';
                roomTotalSpan.textContent = '';
                roomAvailableSpan.textContent = '';
            }
        });

        stateSelect.addEventListener('change', function() {
            const countryId = countrySelect.value;
            const stateId = this.value;
            if (countryId && stateId) {
                fetch(`/user/accommodation/hotels/${countryId}/${stateId}`)
                    .then(response => response.json())
                    .then(hotels => {
                        hotelSelect.innerHTML = '<option value="">Select Hotel</option>';
                        hotelSelect.disabled = false;
                        hotels.forEach(hotel => {
                            const option = document.createElement('option');
                            option.value = hotel.id;
                            option.textContent = hotel.name;
                            hotelSelect.appendChild(option);
                        });
                        roomTypeSelect.disabled = true;
                        roomTypeSelect.innerHTML = '<option value="">Select Room Type</option>';
                        roomDetailsContainer.innerHTML =
                            '<h5>Room Details:</h5><p><strong>Description:</strong> <span id="room-description"></span></p><p><strong>Amenities:</strong> <span id="room-amenities"></span></p><p><strong>Total Rooms:</strong> <span id="room-total"></span></p><p><strong>Available Rooms:</strong> <span id="room-available"></span></p>';
                        roomDescriptionSpan.textContent = '';
                        roomAmenitiesSpan.textContent = '';
                        roomTotalSpan.textContent = '';
                        roomAvailableSpan.textContent = '';
                    });
            } else {
                hotelSelect.disabled = true;
                hotelSelect.innerHTML = '<option value="">Select Hotel</option>';
                roomTypeSelect.disabled = true;
                roomTypeSelect.innerHTML = '<option value="">Select Room Type</option>';
                roomDetailsContainer.innerHTML =
                    '<h5>Room Details:</h5><p><strong>Description:</strong> <span id="room-description"></span></p><p><strong>Amenities:</strong> <span id="room-amenities"></span></p><p><strong>Total Rooms:</strong> <span id="room-total"></span></p><p><strong>Available Rooms:</strong> <span id="room-available"></span></p>';
                roomDescriptionSpan.textContent = '';
                roomAmenitiesSpan.textContent = '';
                roomTotalSpan.textContent = '';
                roomAvailableSpan.textContent = '';
            }
        });

        hotelSelect.addEventListener('change', function() {
            const hotelId = this.value;
            if (hotelId) {
                fetch(`/user/accommodation/room-types/${hotelId}`)
                    .then(response => response.json())
                    .then(roomTypes => {
                        roomTypeSelect.innerHTML = '<option value="">Select Room Type</option>';
                        roomTypeSelect.disabled = false;
                        roomDetailsContainer.innerHTML =
                            '<h5>Room Details:</h5><p><strong>Description:</strong> <span id="room-description"></span></p><p><strong>Amenities:</strong> <span id="room-amenities"></span></p><p><strong>Total Rooms:</strong> <span id="room-total"></span></p><p><strong>Available Rooms:</strong> <span id="room-available"></span></p>';
                        roomDescriptionSpan.textContent = '';
                        roomAmenitiesSpan.textContent = '';
                        // Removed: roomTotalSpan.textContent = '';
                        // Removed: roomAvailableSpan.textContent = '';
                        roomTypes.forEach(roomType => {
                            const option = document.createElement('option');
                            option.value = roomType.id;
                            option.textContent =
                                `${roomType.name} (Price: ${parseFloat(roomType.default_price).toFixed(2)} USD)`;
                            option.dataset.defaultPrice = roomType.default_price;
                            option.dataset.description = roomType.description || '';
                            option.dataset.amenities = roomType.amenities || '';
                            option.dataset.totalRooms = roomType.total_rooms || '';
                            option.dataset.availableRooms = roomType.available_rooms || '';
                            roomTypeSelect.appendChild(option);
                        });
                    });
            } else {
                roomTypeSelect.innerHTML = '<option value="">Select Room Type</option>';
                roomTypeSelect.disabled = true;
                roomDetailsContainer.innerHTML =
                    '<h5>Room Details:</h5><p><strong>Description:</strong> <span id="room-description"></span></p><p><strong>Amenities:</strong> <span id="room-amenities"></span></p><p><strong>Total Rooms:</strong> <span id="room-total"></span></p><p><strong>Available Rooms:</strong> <span id="room-available"></span></p>';
                roomDescriptionSpan.textContent = '';
                roomAmenitiesSpan.textContent = '';
                roomTotalSpan.textContent = '';
                roomAvailableSpan.textContent = '';
            }
        });

        roomTypeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.dataset.description;
            const amenities = selectedOption.dataset.amenities;
            const totalRooms = selectedOption.dataset.totalRooms;
            const availableRooms = selectedOption.dataset.availableRooms;

            console.log('--- Room Type Changed ---');
            console.log('Selected Option Value:', this.value);
            console.log('Description from dataset:', description);
            console.log('Amenities from dataset:', amenities);
            console.log('Total Rooms from dataset:', totalRooms);
            console.log('Available Rooms from dataset:', availableRooms);

            if (roomDescriptionSpan) {
                roomDescriptionSpan.textContent = description || 'No description available.';
                console.log('Description span updated to:', roomDescriptionSpan.textContent);
            } else {
                console.log('roomDescriptionSpan not found.');
            }

            if (roomAmenitiesSpan) {
                roomAmenitiesSpan.textContent = amenities || 'No amenities listed.';
                try {
                    const amenitiesArray = JSON.parse(amenities);
                    roomAmenitiesSpan.textContent = amenitiesArray.join(', ') || 'No amenities listed.';
                    console.log('Amenities span updated to:', roomAmenitiesSpan.textContent);
                } catch (e) {
                    console.log('Amenities is not valid JSON:', amenities);
                }
            } else {
                console.log('roomAmenitiesSpan not found.');
            }

            if (roomTotalSpan) {
                roomTotalSpan.textContent = totalRooms || 'N/A';
                console.log('Total Rooms span updated to:', roomTotalSpan.textContent);
            } else {
                console.log('roomTotalSpan not found.');
            }

            if (roomAvailableSpan) {
                roomAvailableSpan.textContent = availableRooms || 'N/A';
                console.log('Available Rooms span updated to:', roomAvailableSpan.textContent);
            } else {
                console.log('roomAvailableSpan not found.');
            }

            checkAvailability(); // Call checkAvailability when room type changes
        });

        function checkAvailability() {
            const checkInDateInput = document.getElementById('check_in_date');
            const checkOutDateInput = document.getElementById('check_out_date');
            const hotelSelect = document.getElementById('hotel_id');
            const roomTypeSelect = document.getElementById('room_type_id');
            const availabilityMessageDiv = document.getElementById('availability-message');

            const checkInDate = checkInDateInput.value;
            const checkOutDate = checkOutDateInput.value;
            const hotelId = hotelSelect.value;
            const roomTypeId = roomTypeSelect.value;

            console.log('--- Checking Availability ---');
            console.log('hotelId:', hotelId);
            console.log('roomTypeId:', roomTypeId);
            console.log('checkInDate:', checkInDate);
            console.log('checkOutDate:', checkOutDate);

            if (checkInDate && checkOutDate && hotelId && roomTypeId) {
                fetch('/user/accommodation/check-room-availability', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            hotelId: hotelId,
                            roomTypeId: roomTypeId,
                            checkInDate: checkInDate,
                            checkOutDate: checkOutDate
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Availability Response:', data);
                        if (data.available) {
                            availabilityMessageDiv.textContent = 'Room type is available for these dates.';
                            availabilityMessageDiv.className = 'text-success';
                        } else {
                            availabilityMessageDiv.textContent = data.error ||
                                'This room type is not available for the selected dates.';
                            availabilityMessageDiv.className = 'text-danger';
                        }
                    })
                    .catch(error => {
                        console.error('Error checking availability:', error);
                        availabilityMessageDiv.textContent = 'Error checking availability.';
                        availabilityMessageDiv.className = 'text-warning';
                    });
            } else {
                availabilityMessageDiv.textContent = ''; // Clear message if dates are incomplete
                availabilityMessageDiv.className = ''; // Clear any previous class
                console.log('Missing parameters for availability check.');
            }
        }

        checkInDateInput.addEventListener('change', checkAvailability);
        checkOutDateInput.addEventListener('change', checkAvailability);
        hotelSelect.addEventListener('change', checkAvailability);
        // checkAvailability is now called when roomTypeSelect changes

        // Initial state: disable dependent dropdowns
        stateSelect.disabled = true;
        hotelSelect.disabled = true;
        roomTypeSelect.disabled = true;
    });
    </script>

</body>

</html>