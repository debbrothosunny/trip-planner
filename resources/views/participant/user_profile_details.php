<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profileUser['username'] ?? 'User Profile') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css"> </head>
<body>
    <div class="container mt-5">
        <div class="row">
        <div class="col-md-4">
                <div class="card">
                    <img src="/<?= htmlspecialchars($profileUser['profile_photo'] ?? '/image/default_profile.png') ?>"
                         alt="<?= htmlspecialchars($profileUser['name'] ?? 'Profile Picture') ?>"
                         class="card-img-top rounded-circle" style="height: 150px; object-fit: cover; width: 150px; margin: 20px auto; display: block;">
                    <div class="card-body text-center">
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $profileUser['id']) : ?>
                            <div id="follow-button-container">
                                <?php if ($isFollowing) : ?>
                                    <button class="btn btn-danger btn-sm unfollow-btn" data-user-id="<?= $profileUser['id'] ?>">Unfollow</button>
                                <?php else : ?>
                                    <button class="btn btn-primary btn-sm follow-btn" data-user-id="<?= $profileUser['id'] ?>">Follow</button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <h2>User Details</h2>
                <table class="table table-bordered">
                    <tbody>
                        <tr><th>ID</th><td><?= htmlspecialchars($profileUser['id'] ?? 'N/A') ?></td></tr>
                        <tr><th>Name</th><td><?= htmlspecialchars($profileUser['name'] ?? 'N/A') ?></td></tr>
                        <tr><th>Email</th><td><?= htmlspecialchars($profileUser['email'] ?? 'N/A') ?></td></tr>
                        <tr><th>Phone</th><td><?= htmlspecialchars($profileUser['phone'] ?? 'N/A') ?></td></tr>
                        <tr><th>Role</th><td><?= htmlspecialchars($profileUser['role'] ?? 'N/A') ?></td></tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if (isset($profileUser['status'])) : ?>
                                    <?php if ($profileUser['status'] == 0) : ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php elseif ($profileUser['status'] == 1) : ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php else : ?>
                                        <?= htmlspecialchars($profileUser['status']) ?>
                                    <?php endif; ?>
                                <?php else : ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr><th>Country</th><td><?= htmlspecialchars($profileUser['country'] ?? 'N/A') ?></td></tr>
                        <tr><th>City</th><td><?= htmlspecialchars($profileUser['city'] ?? 'N/A') ?></td></tr>
                        <tr><th>Language</th><td><?= htmlspecialchars($profileUser['language'] ?? 'N/A') ?></td></tr>
                        <tr><th>Currency</th><td><?= htmlspecialchars($profileUser['currency'] ?? 'N/A') ?></td></tr>
                        <tr><th>Gender</th><td><?= htmlspecialchars($profileUser['gender'] ?? 'N/A') ?></td></tr>
                    </tbody>
                </table>

                <h2 class="mt-4">Previous Trips</h2>
                <div class="row" id="previous-trips-container">
                    <?php if (!empty($previousTrips)) : ?>
                        <?php foreach ($previousTrips as $prevTrip) : ?>
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm rounded-4">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold"><?= htmlspecialchars($prevTrip['trip_name']) ?></h6>
                                        <p class="card-text small">
                                            <strong>Start:</strong> <?= htmlspecialchars($prevTrip['start_date']) ?><br>
                                            <strong>End:</strong> <?= htmlspecialchars($prevTrip['end_date']) ?><br>
                                            <strong>Budget:</strong> $<?= htmlspecialchars($prevTrip['budget']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p><?= htmlspecialchars($profileUser['username'] ?? 'This User') ?> hasn't created any expired public trips yet.</p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($lastTripItineraries)) : ?>
                    <h2 class="mt-4">Itinerary for Last Expired Trip: <?= htmlspecialchars($lastTrip['trip_name'] ?? '') ?></h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lastTripItineraries as $itinerary) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($itinerary['day_title'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($itinerary['location'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($itinerary['description'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($itinerary['itinerary_date'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif (!empty($expiredTrips)) : ?>
                    <p class="mt-3">No itinerary details found for the last expired trip.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const followButtonContainer = document.getElementById('follow-button-container');
            if (followButtonContainer) {
                followButtonContainer.addEventListener('click', function(event) {
                    let target = event.target;
                    if (target.classList.contains('follow-btn')) {
                        const userIdToFollow = target.dataset.userId;
                        fetch(`/user/${userIdToFollow}/follow`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                target.className = 'btn btn-danger btn-sm unfollow-btn';
                                target.textContent = 'Unfollow';
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    } else if (target.classList.contains('unfollow-btn')) {
                        const userIdToUnfollow = target.dataset.userId;
                        fetch(`/user/${userIdToUnfollow}/unfollow`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                target.className = 'btn btn-primary btn-sm follow-btn';
                                target.textContent = 'Follow';
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    }
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>