<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($header_title) ? $header_title : 'Dashboard'; ?> -</title>

    <!-- Google Font: Poppins -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="/asset/backend/plugins/fontawesome-free/css/all.min.css">

    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="/asset/backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="/asset/backend/dist/css/adminlte.min.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/asset/backend/css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    


<!-- sweet alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



   
</head>
   
<style>
body {
    font-family: 'Roboto', sans-serif;
}

h1,
h2,
h3 {
    font-weight: 800;
    /* Bold for headings */
}

p,
input,
button {
    font-weight: 600;
    /* Normal for text and inputs */
}
</style>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <div class="sidebar">
                <h4 class="text-center text-white mt-3">Dashboard</h4>

                <ul class="nav flex-column">
                    <?php if (isset($_SESSION['role'])): ?>
                    <?php 
                $currentUrl = $_SERVER['REQUEST_URI']; // Get current URL
                ?>

                    <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/admin/dashboard') !== false) ? 'active' : ''; ?>">
                        <a href="/admin/dashboard" class="nav-link">Dashboard</a>
                    </li>

                    

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/admin/hotels') !== false) ? 'active' : ''; ?>">
                        <a href="/admin/hotels" class="nav-link">Hotel</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/admin/hotels/rooms') !== false) ? 'active' : ''; ?>">
                        <a href="/admin/hotels/rooms" class="nav-link">Hotel Room</a>
                    </li>
                    
                
                   
                    <?php elseif ($_SESSION['role'] == 'user'): ?>
                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/dashboard') !== false) ? 'active' : ''; ?>">
                        <a href="/user/dashboard" class="nav-link">Dashboard</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/view-trip') !== false) ? 'active' : ''; ?>">
                        <a href="/user/view-trip" class="nav-link">Trip</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/transportation') !== false) ? 'active' : ''; ?>">
                        <a href="/user/transportation" class="nav-link">Transportation</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/accommodation') !== false) ? 'active' : ''; ?>">
                        <a href="/user/accommodation" class="nav-link">Accommodation</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/expense') !== false) ? 'active' : ''; ?>">
                        <a href="/user/expense" class="nav-link">Expense</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/budget-view') !== false) ? 'active' : ''; ?>">
                        <a href="/user/budget-view" class="nav-link">Budget Track</a>
                    </li>
                    
                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/my_trip_participants') !== false) ? 'active' : ''; ?>">
                        <a href="/user/my_trip_participants" class="nav-link">Trip Participant</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/user/profile') !== false) ? 'active' : ''; ?>">
                        <a href="/user/profile" class="nav-link">My profile</a>
                    </li>


                    <?php elseif ($_SESSION['role'] == 'participant'): ?>
                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/participant/dashboard') !== false) ? 'active' : ''; ?>">
                        <a href="/participant/dashboard" class="nav-link">Dashboard</a>
                    </li>

                    <li
                        class="nav-item <?php echo (strpos($currentUrl, '/participant/profile') !== false) ? 'active' : ''; ?>">
                        <a href="/participant/profile" class="nav-link">My profile</a>
                    </li>

                    <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <!-- Logout Button -->
                <form action="/logout" method="POST" class="text-center mt-3">
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </aside>






        <!-- Content Wrapper. Contains page content -->

        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->





        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>

        <!-- /.control-sidebar -->

        <!-- Main Footer -->


    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->

    <!-- Scripts -->
    <script src="{{ asset('asset/backend/plugins/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap -->
    <script src="{{ asset('asset/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- overlayScrollbars -->
    <script src="{{ asset('asset/backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

    <!-- AdminLTE App -->
    <script src="{{ asset('asset/backend/dist/js/adminlte.js') }}"></script>

    <!-- PAGE PLUGINS -->
    <script src="{{ asset('asset/backend/dist/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('asset/backend/dist/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('asset/backend/dist/jquery-mapael/jquery.mapael.min.js') }}"></script>
    <script src="{{ asset('asset/backend/dist/jquery-mapael/maps/usa_states.min.js') }}"></script>

    <!-- ChartJS -->
    <script src="{{ asset('asset/backend/dist/chart.js/Chart.min.js') }}"></script>

    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('asset/backend/dist/js/demo.js') }}"></script>

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('asset/backend/dist/js/pages/dashboard2.js') }}"></script>




 <!-- Vue js -->
 <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>



<!-- Other Scripts like Bootstrap JS, Custom JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="/asset/backend/js/script.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>