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
            background: #f4f7fc;
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .navbar {
            background-color: #0056b3;
            color: white;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .logout-btn {
            position: absolute;
            right: 20px;
            top: 12px;
        }

        .container {
            margin-top: 30px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background-color: #fff;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 1.25rem;
            font-weight: bold;
            text-align: center;
            border-radius: 12px 12px 0 0;
            padding: 10px;
        }

        .table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-bordered td, .table-bordered th {
            border: 1px solid #ddd;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-info {
            background-color: #17a2b8;
            border: none;
        }

        .btn-info:hover, .btn-danger:hover {
            opacity: 0.85;
        }

        .btn-sm {
            font-size: 0.875rem;
        }

        .card-footer {
            background-color: #f8f9fa;
            text-align: center;
            border-radius: 0 0 12px 12px;
            padding: 10px;
        }

        .fw-bold {
            font-weight: 600;
        }

        .badge {
            padding: 5px 10px;
            font-size: 0.875rem;
        }

        .text-muted {
            color: #aaa;
        }

        .text-success {
            color: #28a745;
        }

        .text-warning {
            color: #ffc107;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <span class="navbar-brand">Admin Dashboard</span>
        <form action="/logout" method="POST" class="logout-btn">
            <button type="submit" class="btn btn-danger btn-sm">Logout</button>
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

        <div class="card mb-4">
            <div class="card-header">Registered Users</div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
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

        <div class="card">
            <div class="card-header text-white" style="background-color: #007bff;">
                Trip Participants
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Trip Name</th>
                            <th>Participants</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['trips'] as $trip): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($trip['name']) ?></td>
                            <td>
                                <?php if (!empty($data['participants'][$trip['id']])): ?>
                                <ul style="padding-left: 20px;">
                                    <?php foreach ($data['participants'][$trip['id']] as $participant): ?>
                                    <li>
                                        <span class="fw-semibold"><?= htmlspecialchars($participant['user_name']) ?></span>
                                        - 
                                        <span class="badge <?= $participant['status'] == 'Confirmed' ? 'bg-success' : 'bg-warning' ?>">
                                            <?= htmlspecialchars($participant['status']) ?>
                                        </span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php else: ?>
                                <span class="text-muted">No participants</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
