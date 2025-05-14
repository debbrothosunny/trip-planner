<?php
$header_title = "Hotel-Room";
// Include layout (or other necessary files)
include __DIR__ . '/../layouts/app.php';

include __DIR__ . '/../sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
}
?>
<?php if (count($hotelRooms) > 0): ?>
<?php endif; ?>
<div class="container mt-5">
    <h2>Hotel Rooms</h2>
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="/admin/hotel-room/create" class="btn btn-primary">Add New Hotel Room</a>
        <div>
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search hotel rooms...">
        </div>
    </div>

    <?php if (!empty($hotelRooms)): ?>
    <table class="table table-bordered" id="hotelRoomsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hotel</th>
                <th>Room Type</th>
                <th>Capacity</th>
                <th>Price</th>
                <th>Description</th>
                <th>Total Rooms</th>
                <th>Available Rooms</th>
                <th>Status</th>
                <th>Amenities</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hotelRooms as $room): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['id']); ?></td>
                <td><?php echo htmlspecialchars($room['hotel_name']); ?></td>
                <td><?php
                    foreach ($roomTypes as $roomType) {
                        if ($roomType['id'] == $room['room_type_id']) {
                            echo htmlspecialchars($roomType['name']);
                            break;
                        }
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                <td><?php echo htmlspecialchars($room['price']); ?></td>
                <td><?php echo htmlspecialchars($room['description']); ?></td>
                <td><?php echo htmlspecialchars($room['total_rooms']); ?></td>
                <td><?php echo htmlspecialchars($room['available_rooms']); ?></td>
                <td><?php echo htmlspecialchars($room['status'] == 0 ? 'Active' : 'Inactive'); ?></td>
                <td><?php echo htmlspecialchars($room['amenities']); ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary edit-hotel-room-btn" data-bs-toggle="modal"
                        data-bs-target="#editHotelRoomModal-<?php echo htmlspecialchars($room['id']); ?>">Edit</button>
                    <a href="/admin/hotel-room/delete/<?php echo htmlspecialchars($room['id']); ?>"
                        class="btn btn-sm btn-danger delete-hotel-room-btn"
                        data-hotel-room-id="<?php echo htmlspecialchars($room['id']); ?>">Delete</a>
                </td>
            </tr>

            <div class="modal fade" id="editHotelRoomModal-<?php echo htmlspecialchars($room['id']); ?>"
                tabindex="-1" aria-labelledby="editHotelRoomModalLabel-<?php echo htmlspecialchars($room['id']); ?>"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="editHotelRoomModalLabel-<?php echo htmlspecialchars($room['id']); ?>">Edit Hotel
                                Room</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="/admin/hotel-room/update/<?php echo htmlspecialchars($room['id']); ?>"
                                method="POST">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="edit_room_type_id-<?php echo htmlspecialchars($room['id']); ?>"
                                            class="form-label">Room Type:</label>
                                        <select class="form-select"
                                            id="edit_room_type_id-<?php echo htmlspecialchars($room['id']); ?>"
                                            name="room_type_id" required>
                                            <option value="">Select Room Type</option>
                                            <?php if (!empty($roomTypes)): ?>
                                            <?php foreach ($roomTypes as $roomType): ?>
                                            <option value="<?php echo htmlspecialchars($roomType['id']); ?>"
                                                <?php echo ($room['room_type_id'] == $roomType['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($roomType['name']); ?></option>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <option value="" disabled>No active room types found</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="edit_hotel_id-<?php echo htmlspecialchars($room['id']); ?>"
                                            class="form-label">Hotel:</label>
                                        <select class="form-select"
                                            id="edit_hotel_id-<?php echo htmlspecialchars($room['id']); ?>"
                                            name="hotel_id" required>
                                            <option value="">Select Hotel</option>
                                            <?php if (!empty($hotels)): ?>
                                            <?php foreach ($hotels as $hotel): ?>
                                            <option value="<?php echo htmlspecialchars($hotel['id']); ?>"
                                                <?php echo ($room['hotel_id'] == $hotel['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($hotel['name']); ?></option>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <option value="" disabled>No hotels found</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_capacity-<?php echo htmlspecialchars($room['id']); ?>"
                                        class="form-label">Capacity:</label>
                                    <input type="number" class="form-control"
                                        id="edit_capacity-<?php echo htmlspecialchars($room['id']); ?>" name="capacity"
                                        value="<?php echo htmlspecialchars($room['capacity']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_price-<?php echo htmlspecialchars($room['id']); ?>"
                                        class="form-label">Price:</label>
                                    <input type="number" step="0.01" class="form-control"
                                        id="edit_price-<?php echo htmlspecialchars($room['id']); ?>" name="price"
                                        value="<?php echo htmlspecialchars($room['price']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_description-<?php echo htmlspecialchars($room['id']); ?>"
                                        class="form-label">Description:</label>
                                    <textarea class="form-control"
                                        id="edit_description-<?php echo htmlspecialchars($room['id']); ?>"
                                        name="description"><?php echo htmlspecialchars($room['description']); ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="edit_total_rooms-<?php echo htmlspecialchars($room['id']); ?>"
                                            class="form-label">Total Rooms:</label>
                                        <input type="number" class="form-control"
                                            id="edit_total_rooms-<?php echo htmlspecialchars($room['id']); ?>"
                                            name="total_rooms"
                                            value="<?php echo htmlspecialchars($room['total_rooms']); ?>" required>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="edit_available_rooms-<?php echo htmlspecialchars($room['id']); ?>"
                                            class="form-label">Available Rooms:</label>
                                        <input type="number" class="form-control"
                                            id="edit_available_rooms-<?php echo htmlspecialchars($room['id']); ?>"
                                            name="available_rooms"
                                            value="<?php echo htmlspecialchars($room['available_rooms']); ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_status-<?php echo htmlspecialchars($room['id']); ?>"
                                        class="form-label">Status:</label>
                                    <select class="form-select"
                                        id="edit_status-<?php echo htmlspecialchars($room['id']); ?>" name="status"
                                        required>
                                        <option value="0" <?php echo ($room['status'] == 0) ? 'selected' : ''; ?>>Active
                                        </option>
                                        <option value="1" <?php echo ($room['status'] == 1) ? 'selected' : ''; ?>>
                                            Inactive</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_amenities-<?php echo htmlspecialchars($room['id']); ?>"
                                        class="form-label">Amenities:</label>
                                    <textarea class="form-control"
                                        id="edit_amenities-<?php echo htmlspecialchars($room['id']); ?>"
                                        name="amenities"><?php echo htmlspecialchars($room['amenities']); ?></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Room</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No hotel rooms found.</p>
    <?php endif; ?>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const hotelRoomsTable = document.getElementById('hotelRoomsTable').getElementsByTagName('tbody')[0];
        const tableRows = hotelRoomsTable.getElementsByTagName('tr');
        const deleteButtons = document.querySelectorAll('.delete-hotel-room-btn');
        const editButtons = document.querySelectorAll('.edit-hotel-room-btn');
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get('success');
        const errorMessage = urlParams.get('error');

        if (successMessage) {
            Swal.fire('Success!', successMessage, 'success');
            history.replaceState(null, null, window.location.pathname);
        }

        if (errorMessage) {
            Swal.fire('Error!', errorMessage, 'error');
            history.replaceState(null, null, window.location.pathname);
        }

        searchInput.addEventListener('input', function() {
            const searchTerm = searchInput.value.toLowerCase();

            for (let i = 0; i < tableRows.length; i++) {
                const rowData = tableRows[i].textContent.toLowerCase();
                if (rowData.includes(searchTerm)) {
                    tableRows[i].style.display = ''; // Show the row
                } else {
                    tableRows[i].style.display = 'none'; // Hide the row
                }
            }
        });

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const hotelRoomId = this.dataset.hotelRoomId;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `/admin/hotel-room/delete/${hotelRoomId}`;
                    }
                });
            });
        });

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                // No SweetAlert needed for simply opening the modal
            });
        });
    });
</script>