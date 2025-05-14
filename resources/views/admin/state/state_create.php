<?php

// Include layout (or other necessary files)
include __DIR__ . '/../layouts/app.php';

include __DIR__ . '/../sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
}
?>
    <div class="container mt-5">
        <h1>Create New State for <?php echo htmlspecialchars($country['name'] ?? 'Country'); ?></h1>

        <form action="/admin/state/store" method="post">
            <input type="hidden" name="country_id" value="<?php echo htmlspecialchars($country['id'] ?? ''); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">State Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Status:</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="active" value="0">
                    <label class="form-check-label" for="active">
                      Active
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="active" value="1">
                    <label class="form-check-label" for="active">
                        InActive
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Save State</button>
            <a href="/admin/country/state/<?php echo htmlspecialchars($country['id'] ?? ''); ?>" class="btn btn-secondary">Back</a>
        </form>
    </div>

