<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportation List</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        max-width: 1100px;
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        margin-top: 20px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .table th,
    .table td {
        vertical-align: middle;
        text-align: center;
    }

    .btn-custom {
        font-size: 14px;
        padding: 5px 10px;
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


        <!-- Only show links if the user is NOT new -->
        <a href="/user/dashboard">Trip</a>
        <a href="/user/transportation">Transportation</a>
        <a href="/user/accommodation">Accommodation</a>
        <a href="/user/expense">Expense</a>
        <a href="/user/budget-view ">Budget Track</a>
        <a href="/user/my_trip_participants">Trip Participant</a>
        <nav class="navbar">
            <form action="/logout" method="POST">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </nav>
    </div>

    <div class="container">
        <h3 class="mb-3 text-center">Transportation List</h3>

        <!-- Include SweetAlert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <?php if (isset($_SESSION['sweetalert'])): ?>
        <script>
        Swal.fire({
            title: "<?php echo $_SESSION['sweetalert']['title']; ?>",
            text: "<?php echo $_SESSION['sweetalert']['text']; ?>",
            icon: "<?php echo $_SESSION['sweetalert']['icon']; ?>"
        });
        </script>
        <?php unset($_SESSION['sweetalert']); ?>
        <?php endif; ?>

        <!-- Display success or error message -->
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Add New Transportation Button -->
        <div class="d-flex justify-content-end mb-3">
            <a href="/user/transportation/create" class="btn btn-primary">Add Transportation</a>
        </div>

        <!-- Transportation Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Trip Name</th>
                        <th>Type</th>
                        <th>Company</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Dep. Date</th>
                        <th>Arr. Date</th>
                        <th>Booking Ref</th>
                        <th>Amount</th> <!-- New column for Amount -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transportations)): ?>
                    <?php foreach ($transportations as $transportation): ?>
                    <tr>
                        <td><?php echo $transportation['id']; ?></td>
                        <td><?php echo $transportation['trip_name']; ?></td>
                        <td><?php echo $transportation['type']; ?></td> <!-- Display the transportation type -->
                        <td><?php echo $transportation['company_name']; ?></td>
                        <td><?php echo $transportation['departure_location']; ?></td>
                        <td><?php echo $transportation['arrival_location']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($transportation['departure_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($transportation['arrival_date'])); ?></td>
                        <td><?php echo $transportation['booking_reference']; ?></td>
                        <td><?php echo number_format($transportation['amount'], 2); ?> USD</td>
                        <!-- Display the Amount -->
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="/user/transportation/edit/<?php echo $transportation['id']; ?>"
                                    class="btn btn-warning btn-sm">Edit</a>
                                <a href="/user/transportation/delete/<?php echo $transportation['id']; ?>"
                                    class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center text-muted">No transportation records found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>


    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>