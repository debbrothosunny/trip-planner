<?php
$header_title = "Accomodation";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
?>

<style>
.sidebar {
    width: 250px;
    background: #2c3e50;
    color: white;
    height: 100vh;
    position: fixed;
    padding-top: 20px;
}

.sidebar a {
    color: white;
    display: flex;
    align-items: center;
    padding: 12px;
    text-decoration: none;
    transition: 0.3s;
}

.sidebar a i {
    margin-right: 10px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #34495e;
}

.content {
    margin-left: 270px;
    padding: 20px;
    width: 100%;
}
</style>

<div class="container mt-4">
    <h2 class="text-center mb-4">Accommodation List</h2>

    <!-- Add New Accommodation Button -->
    <div class="d-flex justify-content-between mb-3">
        <a href="/user/accommodation/create" class="btn btn-primary">Add New Accommodation</a>
    </div>

    <!-- Check if accommodations exist -->
    <?php if (!empty($accommodations)): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Hotel Name</th>
                    <th>Room Type</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Price Per Day</th>
                    <th>Total Rooms</th> <!-- Matches data for total rooms -->
                    <th>Available Rooms</th> <!-- Matches data for available rooms -->
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($accommodations)): ?>
                <?php foreach ($accommodations as $index => $accommodation): ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td><?= htmlspecialchars($accommodation['hotel_name']); ?></td>
                    <td><?= htmlspecialchars($accommodation['room_type'] ?? 'N/A'); ?></td>
                    <!-- Room type from accommodations table -->
                    <td><?= htmlspecialchars(date("Y-m-d h:i A", strtotime($accommodation['check_in_date']))); ?></td>
                    <td><?= htmlspecialchars(date("Y-m-d h:i A", strtotime($accommodation['check_out_date']))); ?></td>

                    <?php
                // Calculate the total price based on check-in and check-out dates
                $checkIn = new DateTime($accommodation['check_in_date']);
                $checkOut = new DateTime($accommodation['check_out_date']);
                $days = $checkIn->diff($checkOut)->days;

                // If check-in and check-out are the same, treat it as 1 day
                if ($days === 0) {
                    $days = 1;
                }

                // Total price calculation
                $totalPrice = $days * $accommodation['price'];
            ?>
                    <td>$<?= number_format($totalPrice, 2); ?></td>
                    <td><?= htmlspecialchars($accommodation['total_rooms']); ?></td> <!-- Total Rooms -->
                    <td><?= htmlspecialchars($accommodation['available_rooms']); ?></td> <!-- Available Rooms -->
                    <td><?= htmlspecialchars($accommodation['description'] ?? 'N/A'); ?></td>
                    <td>
                        <?php if ($accommodation['status'] == 0): ?>
                        <span class="badge bg-success">Pending</span>
                        <?php else: ?>
                        <span class="badge bg-danger">Confirmed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Delete action for accommodation -->
                        <a href="#" onclick="confirmDelete(<?= $accommodation['id']; ?>)" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">No accommodations found.</td> <!-- Updated colspan to 11 -->
                </tr>
                <?php endif; ?>
            </tbody>
        </table>


    </div>
    <?php else: ?>
    <div class="alert alert-warning text-center">No accommodations found.</div>
    <?php endif; ?>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function confirmDelete(id) {
    // SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: 'You cannot revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // If confirmed, redirect to the delete route
            window.location.href = '/user/accommodation/delete/' + id;
        }
    });
}
</script>