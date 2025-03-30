<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f0f2f5;
        }

        .card-profile {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .modal-content {
            border-radius: 12px;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary,
        .btn-success {
            border-radius: 8px;
        }

        .btn-outline-secondary {
            border-radius: 8px;
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="card card-profile p-4 mx-auto" style="max-width: 500px;">
            <h3 class="text-center mb-4">Your Profile</h3>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <p><strong>Name:</strong> <?= htmlspecialchars($user['name'] ?? 'N/A'); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? 'N/A'); ?></p>

            <button type="button" class="btn btn-primary w-100 mt-3" data-bs-toggle="modal"
                data-bs-target="#editProfileModal">
                Edit Profile
            </button>
        </div>

        <div class="text-center mt-4">
            <a href="/user/profile" class="btn btn-outline-secondary px-4">Back</a>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="/user/profile/update" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <small
                                    class="text-muted">(leave blank to keep current password)</small></label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
