<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
    body {
        background: #f4f7fc;
        font-family: 'Arial', sans-serif;
        color: #333;
    }

    .navbar {
        background-color: #0056b3;
        color: white;
        padding: 10px 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
        font-weight: bold;
        font-size: 1.5rem;
    }

    .logout-btn {
        position: absolute;
        right: 20px;
        top: 12px;
    }

    .container {
        margin-top: 30px;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        background-color: #fff;
    }

    .card-header {
        background-color: #007bff;
        color: white;
        font-size: 1.25rem;
        font-weight: bold;
        text-align: center;
        border-radius: 12px 12px 0 0;
        padding: 10px;
    }

    .table th {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }

    .table-striped tbody tr:nth-child(odd) {
        background-color: #f9f9f9;
    }

    .table-bordered td,
    .table-bordered th {
        border: 1px solid #ddd;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
    }

    .btn-info {
        background-color: #17a2b8;
        border: none;
    }

    .btn-info:hover,
    .btn-danger:hover {
        opacity: 0.85;
    }

    .btn-sm {
        font-size: 0.875rem;
    }

    .card-footer {
        background-color: #f8f9fa;
        text-align: center;
        border-radius: 0 0 12px 12px;
        padding: 10px;
    }

    .fw-bold {
        font-weight: 600;
    }

    .badge {
        padding: 5px 10px;
        font-size: 0.875rem;
    }

    .text-muted {
        color: #aaa;
    }

    .text-success {
        color: #28a745;
    }

    .text-warning {
        color: #ffc107;
    }
    </style>
</head>

<body>
    <nav class="navbar">
        <span class="navbar-brand">Admin Dashboard</span>
        <form action="/logout" method="POST" class="logout-btn">
            <button type="submit" class="btn btn-danger btn-sm">Logout</button>
        </form>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Total Users</div>
                    <div class="card-body text-center">
                        <h3 class="display-4"><?= $data['total_users'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Total Trips</div>
                    <div class="card-body text-center">
                        <h3 class="display-4"><?= $data['total_trips'] ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Registered Users</div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['users'] as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= ucfirst($user['role']) ?></td>
                            <td>
                                <a href="/admin/user/<?= $user['id'] ?>/trips" class="btn btn-info btn-sm">
                                    <i class="fa fa-eye"></i> View Trips
                                </a>
                                <a href="/admin/delete/<?= $user['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center fw-bold">
                Trip Participants
            </div>
            <div class="card-body">
                <table class="table table-bordered table-responsive">
                    <thead class="thead-light">
                        <tr>
                            <th>Trip Name</th>
                            <th>Participants</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['trips'] as $trip): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($trip['name']) ?></td>
                            <td>
                                <?php if (!empty($data['participants'][$trip['id']])): ?>
                                <div class="list-group">
                                    <?php foreach ($data['participants'][$trip['id']] as $participant): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span
                                            class="fw-semibold"><?= htmlspecialchars($participant['user_name']) ?></span>

                                        <!-- Show Status -->
                                        <span
                                            class="badge <?= $participant['trip_status'] == 'accepted' ? 'bg-success' : 'bg-warning' ?>">
                                            <?= htmlspecialchars($participant['trip_status']) ?>
                                        </span>

                                        <?php if ($participant['payment_status'] === 'completed' && isset($participant['amount'])): ?>
                                        <!-- Button to open modal with payment details -->
                                        <a href="javascript:void(0)" class="btn btn-info btn-sm"
                                            onclick="loadPaymentDetails(<?= $trip['id'] ?>, <?= $participant['user_id'] ?>)">
                                            View Details
                                        </a>
                                        <?php endif; ?>

                                        <!-- Accept Payment Button -->
                                        <?php if ($participant['trip_status'] == 'accepted' && $participant['payment_status'] === 'pending'): ?>
                                        <a href="/admin/accept-payment/<?= $trip['id'] ?>/<?= $participant['user_id'] ?>"
                                            class="btn btn-success btn-sm">Accept Payment</a>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <span class="text-muted">No participants</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Structure -->
        <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentDetailsModalLabel">Payment Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul id="paymentDetailsList">
                            <li><strong>Amount:</strong> <?= htmlspecialchars($paymentDetails['amount']) ?></li>
                            <li><strong>Payment Method:</strong>
                                <?= htmlspecialchars($paymentDetails['payment_method']) ?></li>
                            <li><strong>Transaction ID:</strong>
                                <?= htmlspecialchars($paymentDetails['transaction_id']) ?></li>
                            <li><strong>Payment Status:</strong>
                                <?= htmlspecialchars($paymentDetails['payment_status']) ?></li>
                            <li><strong>Created At:</strong> <?= htmlspecialchars($paymentDetails['created_at']) ?></li>
                        </ul>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>



    </div>



    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
function loadPaymentDetails(tripId, userId) {
    // Make an AJAX request to fetch payment details
    fetch(`/admin/view-payment-details/${tripId}/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                // Populate the modal with the payment details
                const paymentDetailsList = document.getElementById('paymentDetailsList');
                paymentDetailsList.innerHTML = `
                    
                    <li><strong>Amount:</strong> ${data.amount}</li>
                    <li><strong>Payment Method:</strong> ${data.payment_method}</li>
                    <li><strong>Transaction ID:</strong> ${data.transaction_id}</li>
                    <li><strong>Payment Status:</strong> ${data.payment_status}</li>
                    <li><strong>Created At:</strong> ${data.created_at}</li>
                `;
                
                // Open the modal
                const paymentDetailsModal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
                paymentDetailsModal.show();
            }
        })
        .catch(error => alert('Error loading payment details'));
}
</script>
</body>

</html>