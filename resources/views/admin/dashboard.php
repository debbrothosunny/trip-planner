<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Trip Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php

$sidebarPath = __DIR__ . '/sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
} 
?>
    <div class="main-content">
        <div class="time-info mt-5">
            <i id="timeIcon"></i>
            <span id="greeting"></span>, <span id="currentDate"></span>, <span id="currentTime"></span>
        </div>

        <div class="container-fluid py-4">

            <?php if (!empty($booking_notification)): ?>
            <div class="alert alert-<?= $booking_notification['type'] ?> alert-dismissible fade show" role="alert">
                <?= $booking_notification['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="d-flex align-items-center mb-4">
                <?php if (!empty($profilePhoto)): ?>
                <img src="/<?= htmlspecialchars($profilePhoto) ?>" alt="Profile Picture" class="rounded-circle me-3"
                    width="100" height="100">
                <?php else: ?>
                <img src="/image/default_profile.jpg" alt="Default Image"
                    style="max-width: 100px; max-height: 100px; border-radius: 4px; object-fit: cover; opacity: 0.7;">
                <?php endif; ?>
                <div>
                    <h2 class="mb-1">Hello, <?= htmlspecialchars($userName ?? 'Guest'); ?> 👋</h2>
                    <p class="mb-0">"Ensuring quality and stability for our users!"</p>
                    <?php if ($adminActiveSince): ?>
                    <p class="mb-0"><small>Admin since:
                            <?= htmlspecialchars(date('j F, Y', strtotime($adminActiveSince))) ?></small></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- First row -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-users"></i></div>
                        <div class="card-title">Total Users</div>
                        <div class="card-text"><?= $data['total_users'] ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-truck"></i></div>
                        <div class="card-title">Total Trips</div>
                        <div class="card-text"><?= $data['total_trips'] ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-hiking"></i></div>
                        <div class="card-title">Ongoing Trips</div>
                        <div class="card-text"><?= count($ongoingTrips) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="card-title">Completed Trips</div>
                        <div class="card-text"><?= count($completedTrips) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="card-title">Total Payments</div>
                        <div class="card-text">$<?= number_format($data['total_payment'], 2) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-users-cog"></i></div>
                        <div class="card-title">Total Participants</div>
                        <div class="card-text"><?= $data['total_trip_participants'] ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="card-title">Accepted Participants</div>
                        <div class="card-text"><?= $data['total_accepted'] ?></div>
                    </div>
                </div>
            </div>

            <?php if (!empty($userCountsByCountry)): ?>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-globe"></i></div>
                        <h5 class="card-title">Users by Country</h5>
                        <div style="display: flex; flex-wrap: wrap; min-height: 80px; align-items: flex-start;">
                            <?php foreach ($userCountsByCountry as $countryData): ?>
                            <div style="margin-right: 1rem; margin-bottom: 0.5rem; display: flex; align-items: center;">
                                <span
                                    class="badge bg-secondary"><?= htmlspecialchars($countryData['country']) ?>:</span>
                                <span
                                    class="badge bg-primary rounded-pill ms-2"><?= $countryData['user_count'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($userCountsByCity)): ?>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon"><i class="fas fa-city"></i></div>
                        <h5 class="card-title">Users by City</h5>
                        <div style="display: flex; flex-wrap: wrap; min-height: 80px; align-items: flex-start;">
                            <?php foreach ($userCountsByCity as $cityData): ?>
                            <div style="margin-right: 1rem; margin-bottom: 0.5rem; display: flex; align-items: center;">
                                <span class="badge bg-secondary"><?= htmlspecialchars($cityData['city']) ?>:</span>
                                <span class="badge bg-primary rounded-pill ms-2"><?= $cityData['user_count'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <canvas id="largeDataChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        window.loadPaymentDetails = function(tripId, userId) {
            const details = [
                `Trip ID: ${tripId}`,
                `User ID: ${userId}`,
                `Amount Paid: $500`,
                `Payment Date: 2024-04-12`
            ];

            const list = document.getElementById("paymentDetailsList");
            list.innerHTML = "";
            details.forEach(item => {
                const li = document.createElement("li");
                li.textContent = item;
                list.appendChild(li);
            });

            const modal = new bootstrap.Modal(document.getElementById("paymentDetailsModal"));
            modal.show();
        };
    });

    document.addEventListener("DOMContentLoaded", function() {
        function updateFooter() {
            const now = new Date();
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, "0");
            const seconds = now.getSeconds().toString().padStart(2, "0");
            const ampm = hours >= 12 ? "PM" : "AM";
            hours = hours % 12 || 12;

            const greeting = document.getElementById("greeting");
            const timeIcon = document.getElementById("timeIcon");
            const dateElement = document.getElementById("currentDate"); // Corrected variable name
            const timeElement = document.getElementById("currentTime"); // Corrected variable name

            if (hours >= 5 && hours < 12 && ampm === "AM") {
                greeting.innerText = "Good Morning!";
                timeIcon.className = "fas fa-sun sun-animation";
            } else if (ampm === "PM" && hours < 5) {
                greeting.innerText = "Good Afternoon!";
                timeIcon.className = "fas fa-sun sun-animation";
            } else if (ampm === "PM" && hours >= 5) {
                greeting.innerText = "Good Evening!";
                timeIcon.className = "fas fa-moon moon-animation";
            } else {
                greeting.innerText = "Good Night!";
                timeIcon.className = "fas fa-moon moon-animation";
            }

            const date = now.toLocaleDateString("en-US", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
            });
            dateElement.innerText = date;
            timeElement.innerText = `${hours}:${minutes}:${seconds} ${ampm}`;
        }

        updateFooter();
        setInterval(updateFooter, 1000);
    });


    const monthlyData = <?php echo json_encode($data['monthly_growth']); ?>;

    const monthlyLabels = monthlyData.map(item => item.month);
    const monthlyValues = monthlyData.map(item => item.count);

    new Chart(document.getElementById('largeDataChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Monthly User Growth',
                data: monthlyValues,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            plugins: {
                zoom: {
                    zoom: {
                        wheel: {
                            enabled: true,
                        },
                        pinch: {
                            enabled: true
                        },
                        mode: 'x',
                    }
                }
            }
        }
    });
    </script>


    <style>
    :root {
        --bg-light: #f8f9fa;
        --text-light: #212529;
        --card-light: #ffffff;
        --sidebar-light: #0d6efd;
        --sidebar-text-light: #ffffff;

        --bg-dark: #121212;
        --text-dark: #e0e0e0;
        --card-dark: #333;
        /* Slightly darker for better contrast */
        --sidebar-dark: #333;
        --sidebar-text-dark: #ffffff;
    }

    [data-theme="light"] {
        --bg-color: var(--bg-light);
        --text-color: var(--text-light);
        --card-color: var(--card-light);
        --sidebar-color: var(--sidebar-light);
        --sidebar-text-color: var(--sidebar-text-light);
    }

    [data-theme="dark"] {
        --bg-color: var(--bg-dark);
        --text-color: var(--text-dark);
        --card-color: var(--card-dark);
        --sidebar-color: var(--sidebar-dark);
        --sidebar-text-color: var(--sidebar-text-dark);
    }

    [data-theme="dark"] .card {
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.05);
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-color);
        font-family: 'Segoe UI', sans-serif;
        display: flex;
        min-height: 100vh;
        transition: background-color 0.4s ease-in-out, color 0.4s ease-in-out;
    }

    .sidebar {
        width: 240px;
        background-color: var(--sidebar-color);
        color: var(--sidebar-text-color);
        flex-shrink: 0;
        transition: background-color 0.4s ease-in-out, color 0.4s ease-in-out;
    }

    .sidebar .nav-link {
        color: var(--sidebar-text-color);
        padding: 15px 20px;
        font-weight: 500;
        transition: background-color 0.4s ease-in-out, border-left 0.4s ease-in-out;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-left: 4px solid #fff;
    }

    .main-content {
        flex-grow: 1;
        padding: 1rem 2rem;
        transition: background-color 0.4s ease-in-out, color 0.4s ease-in-out;
    }

    .table thead {
        background-color: #0d6efd;
        color: #fff;
    }

    .table {
        background-color: var(--card-color);
        color: var(--text-color);
        border-collapse: collapse;
    }

    .table th,
    .table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .table tbody tr:nth-child(odd) {
        background-color: #f9f9f9;
    }

    .table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table tbody tr[data-theme="dark"]:nth-child(odd) {
        background-color: #444;
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
        transition: background-color 0.4s ease-in-out;
    }

    .navbar {
        display: none;
    }

    .theme-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 768px) {
        .sidebar {
            display: none;
        }

        .navbar {
            display: block;
            background-color: var(--sidebar-color);
        }

        .main-content {
            padding: 1rem;
        }
    }

    /* Adjust the table for dark mode */
    [data-theme="dark"] .table {
        background-color: #333;
        color: #e0e0e0;
    }

    [data-theme="dark"] .table th,
    [data-theme="dark"] .table td {
        border-color: #444;
    }

    [data-theme="dark"] .table tbody tr:nth-child(odd) {
        background-color: #444;
    }

    [data-theme="dark"] .badge {
        background-color: #2c2c2c;
        color: #e0e0e0;
    }

    [data-theme="dark"] .badge.bg-success {
        background-color: #28a745;
    }

    [data-theme="dark"] .badge.bg-warning {
        background-color: #ffc107;
    }

    [data-theme="dark"] .badge.bg-danger {
        background-color: #dc3545;
    }

    [data-theme="dark"] .btn-primary {
        background-color: #007bff;
    }

    [data-theme="dark"] .btn-success {
        background-color: #28a745;
    }

    [data-theme="dark"] .btn-sm {
        font-size: 0.875rem;
    }

    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        overflow: hidden;
        background-color: var(--card-color);
        /* Use the theme variable */
        color: var(--text-color);
        /* Use the theme variable */
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    .card-body {
        padding: 1.1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .card-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: inherit;
        /* Inherit text color from the card */
    }

    .card-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: inherit;
        /* Inherit text color from the card */
    }

    .card-text {
        font-size: 1.8rem;
        font-weight: 700;
        color: inherit;
        /* Inherit text color from the card */
    }

    /* Dark Mode Styles - Refined */
    [data-theme="dark"] .card {
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.05);
        /* More subtle dark shadow */
    }

    .greeting-box {
        background: linear-gradient(135deg, #0f2027, rgb(12, 28, 34), #2c5364);
        border-left: 5px solid #17a2b8;
    }

    .greeting-box h1 {
        font-size: 2.8rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .greeting-box p {
        font-size: 1.25rem;
        opacity: 0.9;
    }

    .time-info-container {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .time-info {
        text-align: center;
        font-size: 1.1rem;
        font-weight: bold;
        color: #cccccc;
    }

    .time-info i {
        margin-right: 10px;
    }

    .sun-animation,
    .moon-animation {
        animation: spin 5s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }
    </style>
</body>

</html>