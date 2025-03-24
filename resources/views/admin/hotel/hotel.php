<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hotel List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Hotel List</h2>

        <div class="d-flex justify-content-end mb-3">
            <a href="/admin/hotels/create" class="btn btn-primary">Add New Hotel</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($hotels)) : ?>
                    <?php foreach ($hotels as $hotel) : ?>
                    <tr>
                        <td><?= htmlspecialchars($hotel['name']) ?></td>
                        <td><?= htmlspecialchars($hotel['location']) ?></td>
                        <td><?= htmlspecialchars($hotel['description']) ?></td>
                        <td>
                            <a href="/admin/hotels/edit/<?= $hotel['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="/admin/hotels/delete/<?= $hotel['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this hotel?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">No hotels found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="/admin/dashboard" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>