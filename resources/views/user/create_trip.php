<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Trip</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Create New Trip</h3>
                </div>  
                <div class="card-body">
                    <form action="/user/create-trip" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Trip Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter trip name" required>
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget</label>
                            <input type="number" id="budget" name="budget" class="form-control" step="0.01" placeholder="Enter trip budget" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Create Trip</button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="/user/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['success_message'])) {
    echo "<script>
        Swal.fire({
            title: '🎉 Success!',
            text: '{$_SESSION['success_message']}',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4CAF50', // Green button
            background: '#f0f8ff', // Light blue background
            color: '#333', // Dark text color
            timer: 5000, // Auto close after 3 seconds
            showClass: {
                popup: 'animate__animated animate__fadeInDown' // Smooth animation
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp' // Fade out effect
            }
        });
    </script>";
    unset($_SESSION['success_message']); // Clear message after showing
}
?>


</body>
</html>
