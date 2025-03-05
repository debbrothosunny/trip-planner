<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Trip Participants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
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
                <!-- Check if there are participants -->
                <?php if (!empty($participants[$trip['trip_id']])): ?>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Participant Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants[$trip['trip_id']] as $participant): ?>
                        <tr>
                            <td><?= htmlspecialchars($participant['user_name']); ?></td>
                            <td><?= htmlspecialchars($participant['user_email']); ?></td>
                            <td><?= htmlspecialchars($participant['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="alert alert-info text-center">
                    No participants have joined this trip yet.
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <!-- Bootstrap 5 JS (bundle includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>