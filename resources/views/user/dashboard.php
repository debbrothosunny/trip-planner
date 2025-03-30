<?php
$header_title = "Dashboard";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
?>

<style>
body {
    display: flex;
    background-color: #121212;
    color: #f5f5f5;

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





.card {
    background-color: #1f1f1f !important;
    border: none;

    box-shadow: 0 1px 10px rgba(0, 0, 0, 0.5), 0 4px 8px rgba(255, 255, 255, 0.02);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 10px rgba(0, 0, 0, 0.6), 0 6px 12px rgba(255, 255, 255, 0.03);
}

.card .card-title {
    color: #f5f5f5;
    font-weight: 600;
}

.card .card-text {
    color: #ffffff;
}

.btn {
    border-radius: 0.5rem;
}

.table {
    background-color:rgb(92, 60, 60);
    color: #f5f5f5;
    border-radius: 10px;
    overflow: hidden;
}

.table th {
    background-color: #2a2a2a;
    color: #ffffff;
}

.table td {
    background-color: #1f1f1f;
    color: #f5f5f5;
}


</style>





<div class="content">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Trips</h5>
                    <p class="card-text fs-4"><?= count($trips) ?></p>
                </div>
            </div>
        </div>
       
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Ongoing Trips</h5>
                    <p class="card-text fs-4">
                        <?= count(array_filter($trips, fn($t) => strtotime($t['start_date']) <= time() && strtotime($t['end_date']) >= time())) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h5 class="card-title">Completed Trips</h5>
                    <p class="card-text fs-4">
                        <?= count(array_filter($trips, fn($t) => strtotime($t['end_date']) < time())) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['success'])) {
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: '{$_SESSION['success']}',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '/user/dashboard';
            });
        </script>";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: '{$_SESSION['error']}',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '/user/dashboard';
            });
        </script>";
        unset($_SESSION['error']);
    }
    ?>