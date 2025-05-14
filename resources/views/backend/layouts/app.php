<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($header_title) ? $header_title : 'Dashboard'; ?> -</title>

    <!-- Google Font: Poppins -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">


    <!-- Theme style -->
    <link rel="stylesheet" href="/asset/backend/dist/css/adminlte.min.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar">
            <div class="sidebar">
                <?php if (isset($_SESSION['role'])): ?>
                <?php
                $currentUrl = $_SERVER['REQUEST_URI']; // Get current URL
  
               ?>



                <ul class="nav flex-column">
                    <?php if ($_SESSION['role'] == 'user'): ?>
                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/dashboard') !== false) ? 'active' : ''; ?>">
                        <a href="/user/dashboard" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/view-trip') !== false) ? 'active' : ''; ?>">
                        <a href="/user/view-trip" class="nav-link"><i class="bi bi-compass me-2"></i> Trip</a>
                    </li>
                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/transportation') !== false) ? 'active' : ''; ?>">
                        <a href="/user/transportation" class="nav-link"><i class="bi bi-car-front-fill me-2"></i>
                            Transportation</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/accommodation') !== false) ? 'active' : ''; ?>">
                        <a href="/user/accommodation" class="nav-link"><i class="bi bi-house-door-fill me-2"></i>
                            Accommodation</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/expense') !== false) ? 'active' : ''; ?>">
                        <a href="/user/expense" class="nav-link"><i class="bi bi-cash-coin me-2"></i> Expense</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/budget-view') !== false) ? 'active' : ''; ?>">
                        <a href="/user/budget-view" class="nav-link"><i class="bi bi-wallet-fill me-2"></i> Budget
                            Track</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/my_trip_participants') !== false) ? 'active' : ''; ?>">
                        <a href="/user/my_trip_participants" class="nav-link"><i class="bi bi-people-fill me-2"></i>
                            Trip Participant</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/profile') !== false) ? 'active' : ''; ?>">
                        <a href="/user/profile" class="nav-link"><i class="bi bi-person-circle me-2"></i> My profile</a>
                    </li>

                    <?php elseif ($_SESSION['role'] == 'participant'): ?>
                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/participant/dashboard') !== false) ? 'active' : ''; ?>">
                        <a href="/participant/dashboard" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>
                            Dashboard</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/participant/trips') !== false) ? 'active' : ''; ?>">
                        <a href="/participant/trips" class="nav-link"><i class="bi bi-compass me-2"></i> Trips</a>
                    </li>


                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/participant/archived-trips') !== false) ? 'active' : ''; ?>">
                        <a href="/participant/archived-trips" class="nav-link"><i class="bi bi-archive-fill me-2"></i>
                            Archived Trips</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/participant/profile') !== false) ? 'active' : ''; ?>">
                        <a href="/participant/profile" class="nav-link"><i class="bi bi-person-circle me-2"></i> My
                            profile</a>
                    </li>
                    <?php endif; ?>
                </ul>



                <form id="logoutForm" action="/logout" method="POST" class="text-center mt-3">
                    <button type="button" class="btn btn-danger" onclick="confirmLogout()">Logout</button>
                </form>
                <?php endif; ?>
            </div>
        </aside>
    </div>





    <!-- Vue js -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>



    <!-- Other Scripts like Bootstrap JS, Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>


<script>
function confirmLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will be logged out!',
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
}
</script>

<style>
body {
    font-family: 'Roboto', sans-serif;
}

h1,
h2,
h3 {
    font-weight: 800;
}

p,
input,
button,
a {
    font-weight: 600;
}

.main-sidebar {
    background-color:rgb(0, 0, 0);
    /* Dark background for sidebar */
    color: white;
    /* Text color for sidebar */
    width: 250px;
    /* Adjust sidebar width as needed */
    height: 100vh;
    /* Full viewport height */
    position: fixed;
    /* Fixed position */
    top: 0;
    left: 0;
    padding-top: 20px;
}

.main-sidebar .sidebar {
    padding: 0 15px;
}

.main-sidebar .nav-link {
    padding: 10px 15px;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.main-sidebar .nav-link:hover,
.main-sidebar .nav-item.active .nav-link {
    background-color: #495057;
    /* Slightly lighter background on hover/active */
}

.main-sidebar .user-panel {
    border-bottom: 1px solid #495057;
    padding-bottom: 15px;
}

.main-sidebar .user-panel .info a {
    color: white;
}

.main-sidebar .btn-danger {
    margin-top: 20px;
}

.nav-link {
    display: block;
    color: rgb(255, 255, 255);
    padding: 0.5rem 1rem;
    text-decoration: none;
  
}

</style>


</html>