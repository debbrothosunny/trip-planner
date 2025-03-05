<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Trips</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #343a40;
            color: white;
            padding: 20px;
            position: fixed;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin-bottom: 10px;
            background: #495057;
            border-radius: 5px;
            text-align: center;
        }

        .sidebar a:hover {
            background: #6c757d;
        }

        .content {
            margin-left: 270px;
            padding: 20px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h4>Dashboard</h4>
        <p>Welcome, <strong><?= htmlspecialchars($user_name ?? 'Guest'); ?></strong></p>
        
        <?php if (!$isNewUser): ?>
            <a href="/user/dashboard">Trip</a>
            <a href="/user/transportation">Transportation</a>
            <a href="/user/accommodation">Accommodation</a>
            <a href="/user/expense">Expense</a>
            <a href="/user/budget-view">Budget Track</a>
            <a href="/user/my_trip_participants">Trip Participant</a>
        <?php else: ?>
            <p class="alert alert-info">Start by creating your first trip!</p>
        <?php endif; ?>
        
        <nav class="navbar">
            <form action="/logout" method="POST">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </nav>
    </div>

    <div class="content">
        <div class="container mt-4">
            <h2 class="mb-3">Trip Dashboard</h2>
            <a href="/user/create-trip" class="btn btn-success mb-3">+ Add New Trip</a>
            
            <?php if ($isNewUser): ?>
                <div class="alert alert-info">Welcome! Start by creating your first trip.</div>
            <?php elseif (!empty($trips)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Trip Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Budget</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trips as $trip): ?>
                            <tr>
                                <td><?= $trip['id']; ?></td>
                                <td><?= htmlspecialchars($trip['name']); ?></td>
                                <td><?= $trip['start_date']; ?></td>
                                <td><?= $trip['end_date']; ?></td>
                                <td>$<?= number_format($trip['budget'], 2); ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="/user/trip/<?= $trip['id']; ?>/edit" class="btn btn-warning btn-sm me-2">Edit</a>
                                    <a href="/trip/<?= $trip['id'] ?>/itinerary" class="btn btn-success btn-sm me-2">Trip Itinerary</a>
                                    <!-- <a href="/user/trip/<?= $trip['id']; ?>/invitation/send" class="btn btn-primary btn-sm me-2">Send Invitation</a> -->
                                    <a href="/user/trip/delete/<?= $trip['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">No trips found.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['success'])) {
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: '{$_SESSION['success']}',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '/user/dashboard';
            });
        </script>";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: '{$_SESSION['error']}',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '/user/dashboard';
            });
        </script>";
        unset($_SESSION['error']);
    }
    ?>
</body>

</html>
