<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Expenses</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 50px;
            transition: width 0.3s;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            margin-bottom: 8px;
            border-radius: 5px;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background: #495057;
        }

        .sidebar a.active {
            background-color: #007bff;
        }

        /* Content */
        .content {
            margin-left: 270px;
            padding: 20px;
        }

        .table thead {
            background-color: #007bff;
            color: white;
        }

        .table th, .table td {
            text-align: center;
        }

        /* Add Expense Button (Moved to top-right) */
        .add-expense-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 210px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content {
                margin-left: 0;
            }

            .add-expense-btn {
                position: relative;
                margin-top: 10px;
            }
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
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Dashboard</h4>
        <a href="/user/dashboard">Trip</a>
        <a href="/user/transportation">Transportation</a>
        <a href="/user/accommodation">Accommodation</a>
        <a href="/user/expense" class="active">Expense</a>
        <a href="/user/budget-view">Budget Track</a>
        <nav class="navbar">
            <form action="/logout" method="POST">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Expenses for Trip</h1>

        <!-- Add Expense Button -->
        <a href="/user/expense/create" class="btn btn-primary add-expense-btn">
            <i class="fas fa-plus"></i> Add Expense
        </a>

        <?php if (!empty($expenses)): ?>
            <?php $tripName = htmlspecialchars($expenses[0]['trip_name']); ?>
            <h3>Expenses for <?php echo $tripName; ?></h3>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    Trip Expenses List
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
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
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this expense?');">
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
            <div class="alert alert-warning">
                No expenses found for this trip.
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
