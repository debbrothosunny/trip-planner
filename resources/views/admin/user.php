<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $_COOKIE['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<style>
.admin-user-table {
    width: 100%;

}

.admin-user-table th,
.admin-user-table td {
    font-size: 0.9rem;
    /* Adjust as needed */
    padding: 0.6rem 0.4rem;
    /* Adjust padding as needed */
}
</style>

<body>

<?php

$sidebarPath = __DIR__ . '/sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
} 
?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Registered Users
                <div class="mb-2">
                    <input type="text" id="searchInput" class="form-control form-control-sm"
                        placeholder="Search users ">
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])) : ?>
                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <table class="table table-bordered align-middle admin-user-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Currency</th>
                            <th>Profile Photo</th>
                            <th>Language</th>
                            <th>Gender</th>
                            <th>Trips</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)) : ?>
                        <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                            <td><?= ucfirst($user['role'] ?? 'N/A') ?></td>
                            <td>
                                <?php if ($user['status'] == 0) : ?>
                                <span class="badge bg-success">Active</span>
                                <?php else : ?>
                                <span class="badge bg-warning text-dark">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($user['country'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($user['city'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($user['currency'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (!empty($user['profile_photo'])) : ?>
                                <img src="/<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile" width="80"
                                    height="80" class="rounded-circle">
                                <?php else : ?>
                                    <img src="/image/default_profile.jpg" alt="Default Image"
                                    style="max-width: 80px; max-height: 80px; border-radius: 4px; object-fit: cover; opacity: 0.7;">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($user['language'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($user['gender'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (isset($user['trips']) && !empty($user['trips'])) : ?>
                                <button type="button" class="btn btn-info btn-sm view-trips-btn" data-bs-toggle="modal"
                                    data-bs-target="#tripsModal" data-user-id="<?= $user['id'] ?>">
                                    View Trips (<?= count($user['trips']) ?>)
                                </button>
                                <?php else : ?>
                                No trips created.
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($user['status'] == 0) : ?>
                                    <a href="/admin/deactivate/<?= $user['id'] ?>" class="btn btn-warning btn-sm me-2">
                                        <i class="fas fa-ban"></i> Deactivate
                                    </a>
                                    <?php else : ?>
                                    <a href="/admin/activate/<?= $user['id'] ?>" class="btn btn-success btn-sm me-2">
                                        <i class="fas fa-check"></i> Activate
                                    </a>
                                    <?php endif; ?>
                                    <a href="/admin/delete/<?= $user['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this user?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <tr>
                            <td colspan="14">No registered users found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if ($totalPages > 1) : ?>
                <nav aria-label="User Pagination">
                    <ul class="pagination">
                        <?php if ($currentPage > 1) : ?>
                        <li class="page-item">
                            <a class="page-link" href="/admin/user?page=<?= $currentPage - 1 ?>">Previous</a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                            <a class="page-link" href="/admin/user?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages) : ?>
                        <li class="page-item">
                            <a class="page-link" href="/admin/user?page=<?= $currentPage + 1 ?>">Next</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tripsModal" tabindex="-1" aria-labelledby="tripsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tripsModalLabel">User Trips</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="tripsModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const usersTable = document.getElementById('usersTable');
        const tableRows = usersTable.querySelectorAll('tbody tr');
        const tripsModal = document.getElementById('tripsModal');
        const tripsModalBody = document.getElementById('tripsModalBody');
        const viewTripsButtons = document.querySelectorAll('.view-trips-btn');
        const usersData = <?php echo json_encode($users); ?>; // Pass all user data to JavaScript

        searchInput.addEventListener('input', function() {
            const searchTerm = searchInput.value.toLowerCase();
            tableRows.forEach(row => {
                const rowData = row.textContent.toLowerCase();
                row.style.display = rowData.includes(searchTerm) ? '' : 'none';
            });
        });

        viewTripsButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.dataset.userId;
                const user = usersData.find(user => user.id == userId);

                if (user && user.trips && user.trips.length > 0) {
                    let tripsListHTML = '<table class="table table-bordered">';
                    tripsListHTML +=
                        '<thead><tr><th>Name</th><th>Start Date</th><th>End Date</th><th>Budget</th></tr></thead><tbody>';
                    user.trips.forEach(trip => {
                        tripsListHTML += `<tr>
                                                <td>${trip.name}</td>
                                                <td>${trip.start_date || 'N/A'}</td>
                                                <td>${trip.end_date || 'N/A'}</td>
                                                <td>${trip.budget || 'N/A'}</td>
                                            </tr>`; // Adjust property names to match your Trip model
                    });
                    tripsListHTML += '</tbody></table>';
                    tripsModalBody.innerHTML =
                        `<h3>Trips for ${user.name}</h3>${tripsListHTML}`;
                } else {
                    tripsModalBody.innerHTML =
                        `<h3>Trips for ${user.name}</h3>No trips created by this user.`;
                }
            });
        });

        tripsModal.addEventListener('show.bs.modal', function(event) {
            // Optional: You can perform actions when the modal is shown
        });
    });
    </script>
</body>

</html>