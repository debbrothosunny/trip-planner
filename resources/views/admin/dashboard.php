<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Trip Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background: #f8f9fa;
        font-family: 'Segoe UI', sans-serif;
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 240px;
        background-color: #0d6efd;
        color: #fff;
        flex-shrink: 0;
    }

    .sidebar .nav-link {
        color: #fff;
        padding: 15px 20px;
        font-weight: 500;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
        background-color: #0b5ed7;
        border-left: 4px solid #fff;
    }

    .main-content {
        flex-grow: 1;
        padding: 1rem 2rem;
    }

    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        font-weight: bold;
        font-size: 1.2rem;
        background-color: #0d6efd;
        color: #fff;
        border-radius: 1rem 1rem 0 0;
    }

    .table thead {
        background-color: #0d6efd;
        color: #fff;
    }

    .btn-sm i {
        margin-right: 4px;
    }

    .modal-body li {
        margin-bottom: 0.5rem;
    }

    .list-group-item {
        border: none;
        background: #f1f3f5;
        border-radius: 0.5rem;
        margin-bottom: 0.4rem;
        padding: 10px 15px;
    }

    .navbar {
        display: none;
        /* Removed since we're using a sidebar */
    }

    @media (max-width: 768px) {
        .sidebar {
            display: none;
        }

        .navbar {
            display: block;
            background-color: #0d6efd;
        }

        .main-content {
            padding: 1rem;
        }
    }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="text-white mb-4"><i class="fas fa-chart-line me-2"></i>Admin Panel</h4>

        <a href="/admin/dashboard" class="nav-link active">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>

        <a href="/admin/hotels" class="nav-link">
            <i class="fas fa-hotel me-2"></i> Hotel
        </a>

        <a href="/admin/hotels/rooms" class="nav-link">
            <i class="fas fa-door-open me-2"></i> Hotel Room
        </a>

        <a href="/admin/hotel-bookings" class="nav-link">
            <i class="fas fa-book me-2"></i> Hotel Bookings
        </a>


        <form action="/logout" method="POST" class="mt-auto">
            <button type="submit" class="btn btn-light w-100">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-header">Total Users</div>
                        <div class="card-body">
                            <h3 class="display-6"><?= $data['total_users'] ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-header">Total Trips</div>
                        <div class="card-body">
                            <h3 class="display-6"><?= $data['total_trips'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registered Users Table -->
            <div class="card mb-4" id="users">
                <div class="card-header">Registered Users</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle">
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
                                        <i class="fas fa-eye"></i> Trips
                                    </a>
                                    <a href="/admin/delete/<?= $user['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this user?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Trip Participants -->
            <div class="card mb-4" id="trips">
                <div class="card-header text-center">Trip Participants</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
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
                            <?php foreach ($data['trips'] as $trip): ?>
                            <?php 
                        $participants = $data['participants'][$trip['id']] ?? [];
                        $rowspan = max(count($participants), 1);
                        $firstRow = true;
                    ?>

                            <?php if (!empty($participants)): ?>
                            <?php foreach ($participants as $participant): ?>
                            <tr>
                                <?php if ($firstRow): ?>
                                <td class="fw-bold" rowspan="<?= $rowspan ?>">
                                    <?= htmlspecialchars($trip['name']) ?>
                                </td>
                                <?php $firstRow = false; ?>
                                <?php endif; ?>

                                <td><?= htmlspecialchars($participant['user_name']) ?></td>

                                <td>
                                    <span
                                        class="badge <?= $participant['trip_status'] == 'accepted' ? 'bg-success' : 'bg-warning' ?>">
                                        <?= htmlspecialchars($participant['trip_status']) ?>
                                    </span>
                                </td>

                                <td>
                                    <span
                                        class="badge <?= $participant['payment_status'] === 'completed' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= htmlspecialchars($participant['payment_status']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($participant['payment_status'] === 'completed' && isset($participant['amount'])): ?>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm"
                                        onclick="loadPaymentDetails(<?= $trip['id'] ?>, <?= $participant['user_id'] ?>)">
                                        <i class="fas fa-receipt"></i> Details
                                    </a>
                                    <?php endif; ?>

                                    <?php if ($participant['trip_status'] === 'accepted' && $participant['payment_status'] === 'pending'): ?>
                                    <a href="/admin/accept-payment/<?= $trip['id'] ?>/<?= $participant['user_id'] ?>"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-check-circle"></i> Accept Payment
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($trip['name']) ?></td>
                                <td colspan="4"><span class="text-muted">No participants</span></td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-money-check-alt me-2"></i>Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul id="paymentDetailsList" class="list-unstyled"></ul>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function loadPaymentDetails(tripId, userId) {
        fetch(`/admin/view-payment-details/${tripId}/${userId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    const list = document.getElementById('paymentDetailsList');
                    list.innerHTML = `
            <li><strong>Amount:</strong> ${data.amount}</li>
            <li><strong>Method:</strong> ${data.payment_method}</li>
            <li><strong>Transaction ID:</strong> ${data.transaction_id}</li>
            <li><strong>Status:</strong> ${data.payment_status}</li>
            <li><strong>Date:</strong> ${data.created_at}</li>
          `;
                    new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
                }
            });
    }
    </script>

</body>

</html>