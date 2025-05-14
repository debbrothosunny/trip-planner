<?php
$header_title = "Hotel-Bookings";
// Include layout (or other necessary files)
include __DIR__ . '/../layouts/app.php';

include __DIR__ . '/../sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
}
?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Hotel Bookings</h2>
            <div>
                <input type="text" id="searchInput" class="form-control form-control-sm"
                    placeholder="Search bookings...">
            </div>
        </div>
        <?php if (isset($_SESSION['sweetalert'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['sweetalert']['text']); ?>
        </div>
        <?php unset($_SESSION['sweetalert']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($bookings)): ?>
        <table class="table table-bordered" id="bookingsTable">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User Name</th>
                    <th>Hotel Name</th>
                    <th>Country Name</th>
                    <th>State Name</th>
                    <th>Room Type</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Price</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                    <!-- <th>Payment Details</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['accommodation_id']); ?></td>
                    <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['hotel_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['country_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['state_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                    <td><?php echo htmlspecialchars($booking['check_in_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['check_out_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['price']); ?>USD</td>
                    <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                    <td>
                        <?php if ($booking['accommodation_status'] == 1): ?>
                        <span class="badge bg-warning">Pending</span>
                        <?php elseif ($booking['accommodation_status'] == 0): ?>
                        <span class="badge bg-success">Confirmed</span>
                        <?php elseif ($booking['accommodation_status'] == 2): ?>
                        <span class="badge bg-danger">Cancelled</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($booking['accommodation_status'] == 0): ?>
                        <form action="/admin/confirm-booking" method="POST" class="d-inline">
                            <input type="hidden" name="accommodation_id"
                                value="<?php echo htmlspecialchars($booking['accommodation_id']); ?>">
                            <input type="hidden" name="hotel_id"
                                value="<?php echo htmlspecialchars($booking['hotel_id']); ?>">
                            <input type="hidden" name="room_id"
                                value="<?php echo htmlspecialchars($booking['room_id']); ?>">
                            <input type="hidden" name="booked_rooms" value="1">
                            <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                        </form>
                        <form action="/admin/cancel-booking" method="POST" class="d-inline">
                            <input type="hidden" name="accommodation_id"
                                value="<?php echo htmlspecialchars($booking['accommodation_id']); ?>">
                            <button type="submit" class="btn btn-sm btn-danger ms-2">Cancel</button>
                        </form>
                        <?php elseif ($booking['accommodation_status'] == 0): ?>
                        <span class="text-success">Confirmed</span>
                        <?php elseif ($booking['accommodation_status'] == 1): ?>
                        <span class="text-danger">Pending</span>
                        <?php endif; ?>
                    </td>
                    <!-- <td>
                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                            data-bs-target="#paymentDetailsModal"
                            data-accommodation-id="<?php echo htmlspecialchars($booking['accommodation_id']); ?>">
                            Details
                        </button>
                    </td> -->
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No pending hotel bookings found.</p>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentDetailsModalLabel">Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="paymentDetailsBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form action="/admin/update-payment-status" method="POST" class="d-inline">
                        <input type="hidden" name="accommodation_id" id="modal_accommodation_id">
                        <button type="submit" class="btn btn-success">Mark as Paid</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
    <?php if (isset($_SESSION['sweetalert'])): ?>
    Swal.fire({
        title: '<?php echo htmlspecialchars($_SESSION['sweetalert']['title']); ?>',
        text: '<?php echo htmlspecialchars($_SESSION['sweetalert']['text']); ?>',
        icon: '<?php echo htmlspecialchars($_SESSION['sweetalert']['icon']); ?>',
        confirmButtonText: 'Okay'
    });
    <?php endif; ?>

    const paymentDetailsModal = document.getElementById('paymentDetailsModal');
    paymentDetailsModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const accommodationId = button.getAttribute('data-accommodation-id');
        const modalBody = document.getElementById('paymentDetailsBody');
        const modalAccommodationIdInput = document.getElementById('modal_accommodation_id');

        // Set the accommodation ID in the modal's hidden input for the "Mark as Paid" form
        modalAccommodationIdInput.value = accommodationId;

        // Fetch payment details using AJAX
        fetch(`/admin/get-payment-details?accommodation_id=${accommodationId}`)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    let detailsHTML = `
                                    <p><strong>Payment Gateway:</strong> ${data.payment_gateway || 'N/A'}</p>
                                    <p><strong>Payment ID:</strong> ${data.payment_id || 'N/A'}</p>
                                    <p><strong>Amount:</strong> ${data.amount || 'N/A'} ${data.currency || 'N/A'}</p>
                                    <p><strong>Payment Status:</strong> ${data.payment_status || 'N/A'}</p>
                                    <p><strong>Payment Date:</strong> ${data.payment_date || 'N/A'}</p>
                                    <p><strong>Payer ID:</strong> ${data.payer_id || 'N/A'}</p>
                                    <p><strong>Payment Method:</strong> ${data.payment_method || 'N/A'}</p>
                                `;
                    modalBody.innerHTML = detailsHTML;
                } else {
                    modalBody.innerHTML = '<p>No payment details found for this booking.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching payment details:', error);
                modalBody.innerHTML = '<p>Error loading payment details.</p>';
            });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const bookingsTable = document.getElementById('bookingsTable').getElementsByTagName('tbody')[0];
        const tableRows = bookingsTable.getElementsByTagName('tr');

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
    });
    </script>
