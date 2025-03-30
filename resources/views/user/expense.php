<?php
$header_title = "Trip Expenses";
include __DIR__ . '/../backend/layouts/app.php';
?>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
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

        .sidebar a:hover, .sidebar a.active {
            background: #34495e;
        }

        .content {
            margin-left: 270px;
            padding: 20px;
        }

        .table th, .table td {
            text-align: center;
        }

        .add-expense-btn {
            margin-bottom: 15px;
        }
    </style>   



    

    <div class="content">
        <h2 class="mb-3">Trip Expenses</h2>

        <a href="/user/expense/create" class="btn btn-primary add-expense-btn">
            <i class="fas fa-plus"></i> Add Expense
        </a>

        <?php if (!empty($expenses)): ?>
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5>Expenses for <?php echo htmlspecialchars($expenses[0]['trip_name']); ?></h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Currency</th>
                                <th>Description</th>
                                <th>Expense Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($expense['category']); ?></td>
                                <td><?php echo number_format($expense['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($expense['currency']); ?></td>
                                <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                <td><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                                <td>
                                    <a href="/user/expense/edit/<?php echo $expense['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="/user/expense/delete/<?php echo $expense['id']; ?>" method="GET" style="display:inline;">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                No expenses found for this trip.
            </div>
        <?php endif; ?>
    </div>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

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
                window.location.href = '/user/expense';
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
                window.location.href = '/user/expense';
            });
        </script>";
        unset($_SESSION['error']);
    }
    ?>

