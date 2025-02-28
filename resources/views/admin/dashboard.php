<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
    body {
        background: #ffffff;
        font-family: 'Arial', sans-serif;
        color: black;
    }

    .navbar {
        background-color: #f8f9fa;
        color: #000;
        border-bottom: 2px solid #ccc;
        padding: 10px 20px;
    }

    .navbar-brand {
        font-weight: bold;
    }

    .logout-btn {
        position: absolute;
        right: 20px;
        top: 10px;
    }

    .container {
        margin-top: 30px;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
    }

    .card-header {
        background-color: #007bff;
        color: white;
        font-size: 1.3rem;
        font-weight: bold;
        text-align: center;
    }

    .table th {
        background-color: #007bff;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
    }

    .btn-info {
        background-color: #17a2b8;
    }

    .card-footer {
        background-color: #f8f9fa;
        text-align: center;
        border-radius: 0 0 10px 10px;
    }
    </style>
</head>

<body>
    <nav class="navbar">
        <span class="navbar-brand">Admin Dashboard</span>
        <form action="/logout" method="POST" class="logout-btn">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Total Users</div>
                    <div class="card-body text-center">
                        <h3 class="display-4"><?= $data['total_users'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Total Trips</div>
                    <div class="card-body text-center">
                        <h3 class="display-4"><?= $data['total_trips'] ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Registered Users</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['users'] as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= ucfirst($user['role']) ?></td>
                            <td>
                                <a href="/admin/user/<?= $user['id'] ?>/trips" class="btn btn-info btn-sm">
                                    <i class="fa fa-eye"></i> View Trips
                                </a>
                                <a href="/admin/delete/<?= $user['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
