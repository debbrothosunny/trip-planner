<?php
include __DIR__ . '/../backend/layouts/app.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-white text-center">
                    <h3>Edit Trip</h3>
                </div>
                <div class="card-body">
                    <form action="/user/trip/<?php echo $trip['id']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Trip Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($trip['name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $trip['start_date']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $trip['end_date']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget</label>
                            <input type="number" name="budget" id="budget" class="form-control" value="<?php echo $trip['budget']; ?>" required>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Update Trip</button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="/user/view-trip" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>
    </div>
</div>

<?php
// Ensure session is only started if it's not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SweetAlert Handling
if (isset($_SESSION['success'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: '{$_SESSION['success']}',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                Optional: Redirect after success alert closes (if needed)
                window.location.href = '/user/view-trip'; // Uncomment if you want to redirect
            });
        });
    </script>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error!',
                text: '{$_SESSION['error']}',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                Optional: Redirect after error alert closes (if needed)
                window.location.href = '/user/view-trip'; // Uncomment if you want to redirect
            });
        });
    </script>";
    unset($_SESSION['error']);
}
?>



<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


