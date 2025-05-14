<?php
$header_title = "Country Create";
// Include layout (or other necessary files)
include __DIR__ . '/../layouts/app.php';

include __DIR__ . '/../sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
    <div class="container mt-5">
        <h1>Create New Country</h1>  

        <form action="/admin/country/store" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Country Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Status:</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="pending" value="1">
                    <label class="form-check-label" for="pending">
                        Inactive
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="active" value="0">
                    <label class="form-check-label" for="active">
                        Active
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Save Country</button>
            <a href="/admin/country" class="btn btn-secondary">Back</a>
        </form>
    </div>
   