<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $_COOKIE['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - Trip Participants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
<?php

$sidebarPath = __DIR__ . '/sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
} 
?>

    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="card mb-4" id="trips" data-theme="<?php echo $_COOKIE['theme'] ?? 'light'; ?>">
                <div class="card-header text-center d-flex justify-content-between align-items-center">
                    Trip Participants
                    <div class="mb-2">
                        <input type="text" id="searchInput" class="form-control form-control-sm"
                            placeholder="Search...">
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered" id="participantsTable">
                        <thead>
                            <tr>
                                <th>Trip Name</th>
                                <th>Participant</th>
                                <th>Trip Status</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($data['trips'] as $trip) :
                                $participants = $data['participants'][$trip['id']] ?? [];
                                $acceptedParticipants = array_filter($participants, function ($p) {
                                    return $p['trip_status'] === 'accepted';
                                });
                                $rowspan = max(count($acceptedParticipants), 1);
                                $firstRow = true;
                            ?>

                            <?php if (!empty($acceptedParticipants)) : ?>
                            <?php foreach ($acceptedParticipants as $participant) : ?>
                            <tr data-theme="<?php echo $_COOKIE['theme'] ?? 'light'; ?>">
                                <?php if ($firstRow) : ?>
                                <td class="fw-bold" rowspan="<?= $rowspan ?>">
                                    <?= htmlspecialchars($trip['name']) ?>
                                </td>
                                <?php $firstRow = false;
                    endif; ?>
                                <td><?= htmlspecialchars($participant['user_name']) ?></td>
                                <td><span class="badge bg-success">
                                        <?= htmlspecialchars($participant['trip_status']) ?>
                                    </span></td>
                                <td>
                                    <span
                                        class="badge <?= $participant['payment_status'] === 1 ? 'bg-warning' : ($participant['payment_status'] === 0 ? 'bg-success' : 'bg-secondary') ?>">
                                        <?= $participant['payment_status'] === 1 ? 'Pending' : ($participant['payment_status'] === 0 ? 'Completed' : '') ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm me-2"
                                        onclick="loadPaymentDetails(<?= $trip['id'] ?>, <?= $participant['user_id'] ?>)">
                                        <i class="fas fa-receipt"></i> Details
                                    </a>
                                    <?php if ($participant['payment_status'] === 1) : ?>
                                    <button type="button" class="btn btn-success btn-sm"
                                        onclick="acceptPayment(<?= $trip['id'] ?>, <?= $participant['user_id'] ?>)">
                                        <i class="fas fa-check-circle"></i> Accept
                                    </button>
                                    <?php elseif ($participant['payment_status'] === 0) : ?>
                                    <span class="badge bg-success">Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else : ?>
                            <tr data-theme="<?php echo $_COOKIE['theme'] ?? 'light'; ?>">
                                <td class="fw-bold"><?= htmlspecialchars($trip['name']) ?></td>
                                <td colspan="4"><span class="text-muted">No accepted participants</span></td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel"
        aria-hidden="true" data-theme-modal>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-money-check-alt me-2"></i>Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul id="paymentDetailsList" class="list-unstyled">
                        <li><strong>Trip ID:</strong> <span id="payment-detail-trip-id"></span></li>
                        <li><strong>User ID:</strong> <span id="payment-detail-user-id"></span></li>
                        <li><strong>Payment Date:</strong> <span id="payment-detail-date"></span></li>
                        <li><strong>Amount Paid:</strong> <span id="payment-detail-amount"></span></li>
                        <li><strong>Payment Method:</strong> <span id="payment-detail-method"></span></li>
                        <li><strong>Transaction ID:</strong> <span id="payment-detail-transaction-id"></span></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const participantsTable = document.getElementById('participantsTable').getElementsByTagName(
            'tbody')[0];
        const tableRows = participantsTable.getElementsByTagName('tr');

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

    function loadPaymentDetails(tripId, userId) {
        fetch(`/admin/payment-details/${tripId}/${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('payment-detail-trip-id').textContent = data.trip_name || 'N/A';
                    document.getElementById('payment-detail-user-id').textContent = data.user_name || 'N/A';
                    document.getElementById('payment-detail-date').textContent = data.payment_date || 'N/A';
                    document.getElementById('payment-detail-amount').textContent = data.amount ? '$' + data
                        .amount : 'N/A';
                    document.getElementById('payment-detail-method').textContent = data.payment_method || 'N/A';
                    document.getElementById('payment-detail-transaction-id').textContent = data
                        .transaction_id || 'N/A';

                    const paymentDetailsModalElement = document.getElementById('paymentDetailsModal');
                    const paymentDetailsModal = new bootstrap.Modal(paymentDetailsModalElement);
                    const modalContent = paymentDetailsModalElement.querySelector('.modal-content');
                    const modalHeader = paymentDetailsModalElement.querySelector('.modal-header');
                    const modalBody = paymentDetailsModalElement.querySelector('.modal-body');
                    const modalFooter = paymentDetailsModalElement.querySelector('.modal-footer');

                    const currentTheme = getCookie('theme') || 'light'; // Function to get cookie value

                    // Function to apply theme to modal elements
                    function applyTheme(theme) {
                        if (theme === 'dark') {
                            modalContent.classList.add('bg-dark', 'text-light');
                            modalHeader.classList.add('bg-secondary', 'text-light');
                            modalBody.classList.add('bg-dark', 'text-light');
                            modalFooter.classList.add('bg-secondary', 'text-light');
                        } else {
                            modalContent.classList.remove('bg-dark', 'text-light');
                            modalHeader.classList.remove('bg-secondary', 'text-light');
                            modalBody.classList.remove('bg-dark', 'text-light');
                            modalFooter.classList.remove('bg-secondary', 'text-light');
                        }
                    }

                    applyTheme(currentTheme); // Apply theme on modal load
                    paymentDetailsModal.show();

                    // Re-apply theme on 'show.bs.modal' event in case theme changed while modal was hidden
                    paymentDetailsModalElement.addEventListener('show.bs.modal', () => {
                        const currentThemeOnShow = getCookie('theme') || 'light';
                        applyTheme(currentThemeOnShow);
                    });

                } else {
                    Swal.fire('Error', 'No payment details found for this participant.', 'error');
                }
            })
            .catch(error => {
                console.error('Error fetching payment details:', error);
                Swal.fire('Error', 'Failed to fetch payment details.', 'error');
            });
    }

    function acceptPayment(tripId, userId) {
        Swal.fire({
            title: 'Accept Payment?',
            text: 'Are you sure you want to accept the payment for this participant?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, accept it!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/accept-payment/${tripId}/${userId}`, {
                        method: 'POST', // Ensure this is POST
                        headers: {
                            'Content-Type': 'application/json', // Or 'application/x-www-form-urlencoded'
                            // Include CSRF token in headers
                        },
                        body: JSON.stringify({}) // You can send an empty body or specific data if needed
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json(); // Or response.text()
                        }
                        throw new Error('Network response was not ok.');
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Payment Accepted!',
                                data.message || 'The payment has been successfully accepted.',
                                'success'
                            ).then(() => {
                                // Optionally update the UI without a full reload
                                const row = document.querySelector(
                                    `tr[data-user-id="${userId}"][data-trip-id="${tripId}"]`);
                                if (row) {
                                    const paymentStatusCell = row.querySelector(
                                        'td:nth-child(4) span.badge');
                                    const actionCell = row.querySelector('td:nth-child(5)');
                                    if (paymentStatusCell) {
                                        paymentStatusCell.classList.remove('bg-warning');
                                        paymentStatusCell.classList.add('bg-success');
                                        paymentStatusCell.textContent = 'Completed';
                                    }
                                    if (actionCell) {
                                        actionCell.innerHTML =
                                            '<span class="badge bg-success">Completed</span>';
                                    }
                                }
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                data.error || 'Failed to accept the payment.',
                                'error'
                            );
                        }
                    })
                    .catch((error) => {
                        console.error('There was a problem with the fetch operation:', error);
                        Swal.fire(
                            'Error!',
                            'An error occurred while trying to accept the payment.',
                            'error'
                        );
                    });
            }
        });
    }

    // Helper function to get cookie value
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Apply initial theme to the modal if it's already open (though unlikely on page load)
    document.addEventListener('DOMContentLoaded', () => {
        const paymentDetailsModalElement = document.getElementById('paymentDetailsModal');
        paymentDetailsModalElement.addEventListener('show.bs.modal', () => {
            const modalContent = paymentDetailsModalElement.querySelector('.modal-content');
            const modalHeader = paymentDetailsModalElement.querySelector('.modal-header');
            const modalBody = paymentDetailsModalElement.querySelector('.modal-body');
            const modalFooter = paymentDetailsModalElement.querySelector('.modal-footer');
            const currentTheme = getCookie('theme') || 'light';

            if (currentTheme === 'dark') {
                modalContent.classList.add('bg-dark', 'text-light');
                modalHeader.classList.add('bg-secondary', 'text-light');
                modalBody.classList.add('bg-dark', 'text-light');
                modalFooter.classList.add('bg-secondary', 'text-light');
            } else {
                modalContent.classList.remove('bg-dark', 'text-light');
                modalHeader.classList.remove('bg-secondary', 'text-light');
                modalBody.classList.remove('bg-dark', 'text-light');
                modalFooter.classList.remove('bg-secondary', 'text-light');
            }
        });
    });
    </script>
</body>

</html>