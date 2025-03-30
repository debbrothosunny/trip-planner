<?php
include __DIR__ . '/../backend/layouts/app.php';
?>
<style>
body {
    font-family: 'Arial', sans-serif;
}

/* Sidebar Styling */
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
</style>


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
        <div class="text-center mt-3">
            <a href="/user/view-trip" class="btn btn-outline-secondary">Back</a>  
        </div>
    </div>

</div>

<!-- Optional Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>