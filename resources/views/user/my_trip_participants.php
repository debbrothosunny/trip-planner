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

<div class="container mt-5">
    <h2 class="text-center mb-4">Participants in My Trips</h2>

    <!-- Check if there are no trips -->
    <?php if (empty($trips)): ?>
    <div class="alert alert-warning text-center">
        You have not created any trips yet.
    </div>
    <?php else: ?>
    <!-- Loop through trips -->
    <?php foreach ($trips as $trip): ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title"><?= htmlspecialchars($trip['trip_name']); ?></h4>
        </div>
        <div class="card-body">
            <!-- Show Participants with Completed Payment OR Accepted Status -->
            <?php
                $filteredParticipants = array_filter($participants[$trip['trip_id']] ?? [], function ($participant) {
                    return $participant['payment_status'] === 'completed' || $participant['status'] === 'accepted';
                });
            ?>

            <!-- Check if there are participants -->
            <?php if (!empty($filteredParticipants)): ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Participant Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredParticipants as $participant): ?>
                    <tr>
                        <td><?= htmlspecialchars($participant['user_name']); ?></td>
                        <td><?= htmlspecialchars($participant['user_email']); ?></td>
                        <td>
                            <?php if ($participant['payment_status'] === 'completed'): ?>
                            <span class="badge bg-primary">Payment Completed</span> <!-- Changed to Blue -->
                            <?php elseif ($participant['status'] === 'accepted' && $participant['payment_status'] !== 'completed'): ?>
                            <span class="badge" style="background-color: #ff9800; color: white;">Accepted</span> <!-- Changed to Orange -->
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="alert alert-info text-center">
                No participants with completed payments or accepted status.
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Bootstrap 5 JS (bundle includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- Bootstrap 5 JS (bundle includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>