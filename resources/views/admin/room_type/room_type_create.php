<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Room Type</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Create New Room Type</h1>

        <form action="/admin/room-type/store" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Room Type Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Status:</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="active" value="0" checked>
                    <label class="form-check-label" for="active">
                        Active
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="pending" value="1">
                    <label class="form-check-label" for="pending">
                        Inactive
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Save Room Type</button>
            <a href="/admin/room-type" class="btn btn-secondary">Back</a>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>