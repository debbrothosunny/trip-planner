<?php
include __DIR__ . '/../backend/layouts/app.php';
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

            <p><strong>Name:</strong> <?= htmlspecialchars($user['name'] ?? 'N/A'); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
            <p><strong>Country:</strong> <?= htmlspecialchars($user['country'] ?? 'N/A'); ?></p>
            <p><strong>Preferred Language:</strong> <?= htmlspecialchars($user['language'] ?? 'N/A'); ?></p>
            <p><strong>Preferred Currency:</strong> <?= htmlspecialchars($user['currency'] ?? 'N/A'); ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($user['gender'] ?? 'N/A'); ?></p>

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

                <form action="/user/profile/update" method="POST" enctype="multipart/form-data">
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
                                <option value="" <?= ($user['gender'] ?? '') === '' ? 'selected' : '' ?>>Select Gender</option>
                                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                <option value="prefer_not_to_say" <?= ($user['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Profile Photo</label>
                            <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                            <small class="form-text text-muted">Upload a new photo to update (max 2MB, JPEG, PNG).</small>
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

