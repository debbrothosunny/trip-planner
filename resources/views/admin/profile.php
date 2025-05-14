<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $_COOKIE['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<style>
    body.dark-theme {
    background-color: #222;
    color: #eee;
}

.card.dark-theme {
    background-color: #333;
    color: #eee;
    border-color: #555;
}

.btn-primary.dark-theme {
    background-color: #007bff; /* Keep primary color or adjust */
    border-color: #007bff;
    color: #fff;
}
</style>
<body>

<?php

$sidebarPath = __DIR__ . '/sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
} 
?>

<div class="container mt-5">
        <div class="card card-profile p-4 mx-auto" style="max-width: 500px;">
            <h3 class="text-center mb-4">My Profile</h3>

            <?php if (!empty($user['profile_photo'])): ?>
            <div class="text-center mb-3">
                <img src="/<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Picture" class="rounded-circle"
                    width="150" height="150" style="object-fit: cover;">
            </div>
            <?php else: ?>
                <img src="/image/default_profile.jpg" alt="Default Image"
                    style="max-width: 150px; max-height: 150px; border-radius: 4px; object-fit: cover; opacity: 0.7;">
            <?php endif; ?>

            <p><strong>Name:</strong> <span class="profile-info"><?= htmlspecialchars($user['name'] ?? 'N/A'); ?></span></p>
            <p><strong>Email:</strong> <span class="profile-info"><?= htmlspecialchars($user['email'] ?? 'N/A'); ?></span></p>
            <p><strong>Phone:</strong> <span class="profile-info"><?= htmlspecialchars($user['phone'] ?? 'N/A'); ?></span></p>
            <p><strong>Role:</strong> <span class="profile-info"><?= htmlspecialchars($user['role'] ?? 'N/A'); ?></span></p>
            <p><strong>Country:</strong> <span class="profile-info"><?= htmlspecialchars($user['country'] ?? 'N/A'); ?></span></p>
            <p><strong>City:</strong> <span class="profile-info"><?= htmlspecialchars($user['city'] ?? 'N/A'); ?></span></p>
            <p><strong>Preferred Language:</strong> <span class="profile-info"><?= htmlspecialchars($user['language'] ?? 'N/A'); ?></span></p>
            <p><strong>Preferred Currency:</strong> <span class="profile-info"><?= htmlspecialchars($user['currency'] ?? 'N/A'); ?></span></p>
            <p><strong>Gender:</strong> <span class="profile-info"><?= htmlspecialchars($user['gender'] ?? 'N/A'); ?></span></p>

            <button type="button" class="btn btn-primary w-100 mt-3" data-bs-toggle="modal"
                data-bs-target="#editProfileModal">
                Edit Profile
            </button>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="/admin/profile/update" method="POST" enctype="multipart/form-data" id="editProfileForm">
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
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="country" name="country"
                                value="<?= htmlspecialchars($user['country'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="language" class="form-label">Preferred Language</label>
                            <input type="text" class="form-control" id="language" name="language"
                                value="<?= htmlspecialchars($user['language'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="currency" class="form-label">Preferred Currency</label>
                            <input type="text" class="form-control" id="currency" name="currency"
                                value="<?= htmlspecialchars($user['currency'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="" <?= ($user['gender'] ?? '') === '' ? 'selected' : '' ?>>Select Gender
                                </option>
                                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male
                                </option>
                                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>
                                    Female
                                </option>
                                <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other
                                </option>
                                <option value="prefer_not_to_say"
                                    <?= ($user['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not
                                    to
                                    say</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Profile Photo</label>
                            <input type="file" class="form-control" id="profile_photo" name="profile_photo"
                                accept="image/*">
                            <small class="form-text text-muted">Upload a new photo to update (max 2MB, JPEG,
                                PNG).</small>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <small class="text-muted">(leave blank
                                    to
                                    keep current password)</small></label>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editProfileForm = document.getElementById('editProfileForm');
            editProfileForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);

                fetch('/admin/profile/update', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload(); // Reload the page to show updated data
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating profile:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An unexpected error occurred.',
                    });
                });
            });
        });

        // Function to toggle dark/light theme
        function toggleTheme() {
            const body = document.body;
            const card = document.querySelector('.card-profile');
            const modalContent = document.querySelector('.modal-content');
            const modalHeader = document.querySelector('.modal-header');
            const modalFooter = document.querySelector('.modal-footer');
            const profileInfo = document.querySelectorAll('.profile-info');
            const themeIcon = document.getElementById('theme-toggle-icon'); // Assuming you have a theme toggle button

            body.classList.toggle('bg-dark');
            body.classList.toggle('text-light');
            card.classList.toggle('bg-dark');
            card.classList.toggle('text-light');
            modalContent.classList.toggle('bg-dark');
            modalContent.classList.toggle('text-light');
            modalHeader.classList.toggle('bg-dark');
            modalHeader.classList.toggle('text-light');
            modalFooter.classList.toggle('bg-dark');
            modalFooter.classList.toggle('text-light');

            profileInfo.forEach(span => {
                span.classList.toggle('text-light');
            });

            if (themeIcon) {
                themeIcon.classList.toggle('bi-sun-fill');
                themeIcon.classList.toggle('bi-moon-fill');
            }

            // Store the theme preference in local storage
            const currentTheme = body.classList.contains('bg-dark') ? 'dark' : 'light';
            localStorage.setItem('theme', currentTheme);
        }

        // Check for saved theme preference on page load
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                toggleTheme(); // Apply dark theme if saved
            }
        });
    </script>
</body>

</html>