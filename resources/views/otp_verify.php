<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];  // User's email address
    $enteredOtp = $_POST['otp'];  // OTP entered by the user

    // Assuming you have a database connection setup ($this->db)
    $stmt = $this->db->prepare("SELECT verification_token, otp_expiry FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $storedOtp = $user['verification_token'];
        $expiryTime = $user['otp_expiry'];

        // Check if OTP is correct and hasn't expired
        if ($enteredOtp == $storedOtp && time() <= $expiryTime) {
            // OTP is valid
            $stmt = $this->db->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE email = ?");
            $stmt->execute([$email]);

            // Set success message in session
            $_SESSION['message'] = 'Your email has been verified successfully! You can now <a href="/login">log in</a>.';

            // Redirect to login page
            header("Location: login.php");
            exit();
        } else {
            // Invalid or expired OTP
            $_SESSION['message'] = 'Invalid or expired OTP! Please try again.';
            header("Location: otp_verify.php"); // Redirect back to OTP verification page
            exit();
        }
    } else {
        // User not found
        $_SESSION['message'] = 'User not found!';
        header("Location: otp_verify.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4" style="max-width: 500px; width: 100%; border-radius: 15px;">
            <h3 class="text-center mb-4">Verify OTP</h3>

            <!-- Display the session message if it exists -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-info text-center">
                    <?php echo $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); // Clear the message after displaying ?>
                </div>
            <?php endif; ?>

            <!-- OTP Verification Form -->
            <form method="POST" action="verify-otp">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="otp" class="form-label">Enter OTP</label>
                    <input type="text" name="otp" class="form-control" id="otp" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Verify OTP</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>