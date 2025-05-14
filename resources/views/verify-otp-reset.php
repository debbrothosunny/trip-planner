<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* Your custom styles */
    body {
        background-color: #f8f9fa;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .card {
        width: 100%;
        max-width: 400px;
    }
    </style>
</head>

<body class="bg-light">
    <div class="container">
        <div class="card p-4 shadow">
            <h3 class="text-center mb-3">Verify OTP</h3>
            <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>


            <p class="mb-3">Please enter the OTP sent to:
                <strong><?php echo htmlspecialchars($_GET['email'] ?? 'your email address'); ?></strong>
            </p>


            <form action="/verify-otp-reset" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
                <div class="mb-3">
                    <label for="otp" class="form-label">OTP</label>
                    <input type="number" class="form-control" id="otp" name="otp" placeholder="Enter OTP" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
                <p class="mt-3 text-center"><a href="/forgot-password">Resend OTP</a> (Not implemented here)</p>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>