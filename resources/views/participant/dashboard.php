<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h2 class="mb-4">Participant Dashboard</h2>

        <!-- Pending Trip Invitations -->
        <h3>Pending Trip Invitations</h3>
        <?php if (!empty($data['pendingInvitations'])): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Trip Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Budget</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['pendingInvitations'] as $trip): ?>
                        <tr>
                            <td><?= htmlspecialchars($trip['name']) ?></td>
                            <td><?= htmlspecialchars($trip['start_date']) ?></td>
                            <td><?= htmlspecialchars($trip['end_date']) ?></td>
                            <td><?= htmlspecialchars($trip['budget']) ?></td>
                            <td>
                                <form action="/participant/trip/<?= $trip['id'] ?>/accept" method="POST" class="d-inline">
                                    <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                </form>
                                <form action="/participant/trip/<?= $trip['id'] ?>/decline" method="POST" class="d-inline">
                                    <button type="submit" class="btn btn-danger btn-sm">Decline</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending invitations.</p>
        <?php endif; ?>

        <!-- Accepted Trips -->
        <h3 class="mt-5">My Accepted Trips</h3>
        <?php if (!empty($data['acceptedTrips'])): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Trip Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Budget</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['acceptedTrips'] as $trip): ?>
                        <tr>
                            <td><?= htmlspecialchars($trip['name']) ?></td>
                            <td><?= htmlspecialchars($trip['start_date']) ?></td>
                            <td><?= htmlspecialchars($trip['end_date']) ?></td>
                            <td><?= htmlspecialchars($trip['budget']) ?></td>
                            <td>
                                <a href="/participant/trip/<?= $trip['id'] ?>" class="btn btn-primary btn-sm">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No accepted trips.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
