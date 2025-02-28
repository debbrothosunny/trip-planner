<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Itineraries</title>
    <!-- Bootstrap 5 CDN link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f6f9;
        margin: 0;
        padding: 0;
    }

    /* Sidebar Styling */
    .sidebar {
        width: 250px;
        background: #343a40;
        color: white;
        height: 100vh;
        padding-top: 50px;
        position: fixed;
        transition: width 0.3s;
    }

    .sidebar h4 {
        color: #fff;
        text-align: center;
        margin-bottom: 30px;
    }

    .sidebar a {
        color: white;
        display: block;
        padding: 12px 20px;
        text-decoration: none;
        margin-bottom: 8px;
        border-radius: 5px;
        font-size: 1rem;
        transition: background 0.3s;
    }

    .sidebar a:hover {
        background: #495057;
    }

    .sidebar a.active {
        background-color: #007bff;
    }

    /* Content Area */
    .content {
        margin-left: 260px;
        padding: 30px;
    }

    .content h1 {
        color: #333;
        font-size: 2rem;
        margin-bottom: 30px;
    }

    .table thead {
        background-color: #007bff;
        color: white;
    }

    .table th,
    .table td {
        text-align: center;
    }

    .table-striped tbody tr:nth-child(odd) {
        background-color: #f8f9fa;
    }

    .card {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #007bff;
        color: white;
        font-size: 1.25rem;
    }

    .btn-floating {
        position: fixed;
        bottom: 20px;
        right: 20px;
        border-radius: 50%;
        background-color: #007bff;
        color: white;
        width: 60px;
        height: 60px;
        font-size: 24px;
        text-align: center;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s;
    }

    .btn-floating:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    .alert {
        margin-top: 20px;
        padding: 10px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sidebar {
            width: 200px;
        }

        .content {
            margin-left: 210px;
        }
    }

    @media (max-width: 576px) {
        .sidebar {
            width: 100%;
            height: auto;
        }

        .content {
            margin-left: 0;
        }
    }
    </style>

    <div class="sidebar">
        <h4>Dashboard</h4>
        <!-- Only show links if the user is NOT new -->
        <a href="/user/dashboard">Trip</a>
        <a href="/user/transportation">Transportation</a>
        <a href="/user/accommodation">Accommodation</a>
        <a href="/user/expense">Expense</a>
        <nav class="navbar">
            <form action="/logout" method="POST">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </nav>

    </div>

    <div class="container mt-5">
        <h2 class="mb-4">Trip Itineraries</h2>

        <!-- Link to create a new itinerary -->
        <a href="/trip/<?= $trip_id ?>/itinerary/create" class="btn btn-success mb-3">Create Trip Itinerary</a>

        <!-- Table for itineraries -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($itineraries)): ?>
                    <!-- Display itineraries -->
                    <?php foreach ($itineraries as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['day_title']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['itinerary_date']) ?></td>
                        <td>
                            <a href="/trip/<?= $trip_id ?>/itinerary/<?= $row['id'] ?>/edit"
                                class="btn btn-warning btn-sm">Edit</a>
                            <a href="/trip/<?= htmlspecialchars($trip_id) ?>/itinerary/<?= htmlspecialchars($row['id']) ?>/delete"
                                class="btn btn-danger btn-sm" onclick="return confirm('Delete itinerary?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5">No itineraries found for this trip.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>

    <!-- Optional Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>