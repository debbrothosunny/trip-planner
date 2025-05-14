<?php

// Include layout (or other necessary files)
include __DIR__ . '/../layouts/app.php';

include __DIR__ . '/../sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
}
?>
<div class="container mt-5">
    <h1>States for <?php echo htmlspecialchars($country['name'] ?? 'Country'); ?></h1>

    <?php if (!empty($states)): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($states as $state): ?>
            <tr>
                <td><?php echo htmlspecialchars($state['id']); ?></td>
                <td><?php echo htmlspecialchars($state['name']); ?></td>
                <td>
                    <?php
                                    if ($state['status'] == 0) {
                                        echo '<span class="badge bg-warning">Active</span>';
                                    } else {
                                        echo '<span class="badge bg-success">Inactive</span>';
                                    }
                                ?>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#editStateModal-<?php echo htmlspecialchars($state['id']); ?>">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <a href="/admin/state/delete/<?php echo htmlspecialchars($state['id']); ?>"
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this state?')">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>

            <div class="modal fade" id="editStateModal-<?php echo htmlspecialchars($state['id']); ?>" tabindex="-1"
                aria-labelledby="editStateModalLabel-<?php echo htmlspecialchars($state['id']); ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="editStateModalLabel-<?php echo htmlspecialchars($state['id']); ?>">Edit State</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="/admin/state/update/<?php echo htmlspecialchars($state['id']); ?>" method="post">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name-<?php echo htmlspecialchars($state['id']); ?>"
                                        class="form-label">State Name:</label>
                                    <input type="text" class="form-control"
                                        id="name-<?php echo htmlspecialchars($state['id']); ?>" name="name"
                                        value="<?php echo htmlspecialchars($state['name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status"
                                            id="pending-<?php echo htmlspecialchars($state['id']); ?>" value="0"
                                            <?php echo ($state['status'] == 0) ? 'checked' : ''; ?>>
                                        <label class="form-check-label"
                                            for="pending-<?php echo htmlspecialchars($state['id']); ?>">
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status"
                                            id="active-<?php echo htmlspecialchars($state['id']); ?>" value="1"
                                            <?php echo ($state['status'] == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label"
                                            for="active-<?php echo htmlspecialchars($state['id']); ?>">
                                            Inactive
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update State</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="alert alert-info">No states found for this country.</p>
    <?php endif; ?>

    <p><a href="/admin/country/state/create/<?php echo htmlspecialchars($country['id'] ?? ''); ?>"
            class="btn btn-success">Add New State</a></p>
</div>

