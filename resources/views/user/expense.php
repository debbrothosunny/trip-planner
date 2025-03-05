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
</head>

<body>
    <div class="sidebar">
        <h4 class="text-center">Dashboard</h4>
        <a href="/user/dashboard"> Trip</a>
        <a href="/user/transportation"> Transportation</a>
        <a href="/user/accommodation"> Accommodation</a>
        <a href="/user/expense" class="active"> Expense</a>
        <a href="/user/budget-view"> Budget Track</a>
        <a href="/user/my_trip_participants">Trip Participant</a>
        <form action="/logout" method="POST" class="text-center mt-3">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>

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
                                    <form action="/user/expense/delete/<?php echo $expense['id']; ?>" method="POST" style="display:inline;">
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
</body>

</html>
