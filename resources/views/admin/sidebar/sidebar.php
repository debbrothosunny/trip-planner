<?php
$sidebarPath = __DIR__ . '/resources/views/admin/sidebar/sidebar.php';
if (file_exists($sidebarPath)) {
    include $sidebarPath;
} 

?>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-white mb-4 text-center"><i class="fas fa-route me-2"></i> Tripify</h4>

    <a href="/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>

    <a href="/admin/user" class="nav-link"><i class="fas fa-users me-2"></i> Users</a>
    <a href="/admin/trip-participant" class="nav-link" id="tripParticipantsLink">
        <i class="fas fa-walking me-2"></i> Trip Participants
    </a>
    <a href="/admin/country" class="nav-link"><i class="fas fa-flag me-2"></i> Country</a>
    <a href="/admin/hotel" class="nav-link"><i class="fas fa-hotel me-2"></i> Hotels</a>
    <a href="/admin/room-type" class="nav-link"><i class="fas fa-tag me-2"></i> Room Type</a>
    <a href="/admin/hotel-room" class="nav-link"><i class="fas fa-bed me-2"></i> Hotel Rooms</a>
    <a href="/admin/profile" class="nav-link"><i class="fas fa-user me-2"></i>My Profile</a>
    <a href="/admin/hotel-bookings" class="nav-link"><i class="fas fa-calendar-check me-2"></i> Hotel Bookings</a>


    <div class="theme-toggle text-white">
        <span><i class="fas fa-moon me-2"></i> Dark Mode</span>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="themeSwitch">
        </div>
    </div>

    <form id="logoutForm" action="/logout" method="POST" class="mt-auto" onsubmit="return confirmLogout()">
        <button type="submit" class="btn btn-light w-100">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeSwitch = document.getElementById('themeSwitch');

    // Load theme from localStorage on page load
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
        if (themeSwitch) {
            themeSwitch.checked = savedTheme === 'dark';
        }
    }

    // Toggle theme on switch change
    if (themeSwitch) {
        themeSwitch.addEventListener('change', function() {
            const newTheme = this.checked ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            document.cookie = "theme=" + newTheme;
        });
    }
});

function confirmLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logoutForm').submit();
        }
    });
    return false;
}
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
    --card-dark: #1f1f1f;
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

.card {
    border: none;
    border-radius: 1rem;
    background-color: var(--card-color);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: background-color 0.4s ease-in-out, box-shadow 0.4s ease-in-out;
}

.card-header {
    font-weight: bold;
    font-size: 1.2rem;
    background-color: #0d6efd;
    color: #fff;
    border-radius: 1rem 1rem 0 0;
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
    background-color: rgb(0, 0, 0);
}

.table tbody tr:hover {
    background-color: #f1f1f1;
}

.table tbody tr[data-theme="dark"]:nth-child(odd) {
    background-color: #333333;
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
    background-color: #333333;
    /* Darker background for the table */
    color: #e0e0e0;
    /* Light text color */
}

[data-theme="dark"] .table th,
[data-theme="dark"] .table td {
    border-color: #444444;
    /* Lighter border color for dark mode */
}

[data-theme="dark"] .table tbody tr:nth-child(odd) {
    background-color: #444444;
    /* Dark background for odd rows */
}

[data-theme="dark"] .table tbody tr:nth-child(even) {
    background-color: #555555;
    /* Even darker background for even rows */
}

[data-theme="dark"] .badge {
    background-color: #2c2c2c;
    /* Darker background for badges */
    color: #e0e0e0;
    /* Light text color for badges */
}

[data-theme="dark"] .badge.bg-success {
    background-color: #28a745;
    /* Green for success */
}

[data-theme="dark"] .badge.bg-warning {
    background-color: #ffc107;
    /* Yellow for warning */
}

[data-theme="dark"] .badge.bg-danger {
    background-color: #dc3545;
    /* Red for danger */
}

[data-theme="dark"] .btn-primary {
    background-color: #007bff;
    /* Primary button in dark mode */
}

[data-theme="dark"] .btn-success {
    background-color: #28a745;
    /* Success button in dark mode */
}

[data-theme="dark"] .btn-sm {
    font-size: 0.875rem;
    /* Smaller button size in dark mode */
}

[data-theme="light"] .card-body {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: inherit;
    /* To match the card's rounded corners */
    background-color: var(--card-color);
    /* Ensure the background is still white */
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    /* Adjust padding as needed */
    text-decoration: none;
    /* If you want to remove underlines */
}



.me-2 {
    margin-right: 0.5rem;
}
</style>