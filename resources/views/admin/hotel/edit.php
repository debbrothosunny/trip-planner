<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hotel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4>Edit Hotel</h4>
        </div>
        <div class="card-body">
            <!-- Show error message if exists -->
            <?php if (isset($_SESSION['error'])) : ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form action="/admin/hotels/update/<?= $hotel['id'] ?>" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Hotel Name</label>
                    <input type="text" name="name" id="name" class="form-control" 
                           value="<?= htmlspecialchars($hotel['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-control" 
                           value="<?= htmlspecialchars($hotel['location']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required><?= htmlspecialchars($hotel['description']) ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/admin/hotels" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-success">Update Hotel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
