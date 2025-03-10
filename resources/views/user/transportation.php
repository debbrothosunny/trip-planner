<?php
$header_title = "Transportation";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
?>

<style>
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

.content {
    margin-left: 270px;
    padding: 20px;
    width: 100%;
}
</style>

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
            <thead>
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