<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Invitation</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="max-width: 500px; width: 100%;">
            <div class="card-header text-center">
                <h4 class="mb-0">Send Invitation</h4>
            </div>
            <div class="card-body">
                <!-- Display the trip name -->
                <?php if (isset($trip) && isset($trip['name'])): ?>
                    <div class="alert alert-info">
                        You're inviting someone to join the trip: <strong><?= htmlspecialchars($trip['name']); ?></strong>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        Sorry, no trip information available.
                    </div>
                <?php endif; ?>
                
                <!-- Invitation Form -->
                <form action="/user/trip/<?php echo htmlspecialchars($trip_id); ?>/invitation/send" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($trip_id); ?>">
                    <input type="hidden" name="inviter_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

                    <div class="mb-3">
                        <label for="invitee_email" class="form-label">Invitee Email</label>
                        <input type="email" class="form-control" name="invitee_email" required>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Send Invitation</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        // Bootstrap 5 form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>

</html>
