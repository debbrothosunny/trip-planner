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
    <h2 class="text-center mb-4">Manage My Trips & Participants</h2>

    <?php if (empty($trips)): ?>
    <div class="alert alert-warning text-center">
        You have not created any trips yet.
    </div>
    <?php else: ?>
    <?php foreach ($trips as $trip): ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0"><?= htmlspecialchars($trip['trip_name']); ?></h4>
        </div>
        <div class="card-body">
            <h5 class="mb-3">All Participants:</h5>
            <?php if (isset($participants[$trip['trip_id']]) && !empty($participants[$trip['trip_id']])): ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Participant Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants[$trip['trip_id']] as $participant): ?>
                    <tr>
                        <td><?= htmlspecialchars($participant['user_name']); ?></td>
                        <td><?= htmlspecialchars($participant['user_email']); ?></td>
                        <td>
                            <?php if ($participant['status'] === 'pending'): ?>
                            <span class="badge bg-secondary">Pending</span>
                            <?php elseif ($participant['status'] === 'accepted'): ?>
                            <span class="badge" style="background-color: #ff9800; color: white;">Accepted</span>
                            <?php elseif ($participant['status'] === 'rejected'): ?>
                            <span class="badge bg-danger">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($participant['payment_status'] === 'completed'): ?>
                            <span class="badge bg-success">Completed</span>
                            <?php else: ?>
                            <span class="badge bg-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="alert alert-info text-center">
                No participants joined this trip yet.
            </div>
            <?php endif; ?>

            <hr class="my-4">
            <h5 class="mb-3">Pending Itinerary Edit Requests:</h5>
            <?php if (isset($editRequests[$trip['trip_id']]) && !empty($editRequests[$trip['trip_id']])): ?>
            <div class="list-group">
                <?php foreach ($editRequests[$trip['trip_id']] as $request): ?>
                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        
                        <?php if (isset($itineraryItems[$request['itinerary_id']])): ?>
                        <small>Itinerary:
                            <?= htmlspecialchars($itineraryItems[$request['itinerary_id']]['day_title']); ?></small><br>
                        <?php else: ?>
                        <small class="text-danger">Itinerary item not found</small><br>
                        <?php endif; ?>
                        <strong>Reason:</strong> <?= nl2br(htmlspecialchars($request['notes'])); ?>
                        <div class="mt-2">
                            <form method="POST" action="/admin/itinerary-request/accept/<?= $request['id']; ?>">
                                <input type="hidden" name="trip_id" value="<?= htmlspecialchars($trip['trip_id']); ?>">
                                <input type="hidden" name="itinerary_id"
                                    value="<?= htmlspecialchars($request['itinerary_id']); ?>">
                                <button type="submit" class="btn btn-sm btn-success">Accept</button>
                            </form>
                            <form method="POST" action="/admin/itinerary-request/reject/<?= $request['id']; ?>"
                                class="mt-1">
                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        </div>
                    </div>
                    <span class="badge bg-warning rounded-pill">Pending</span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info text-center">
                No pending itinerary edit requests for this trip.
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