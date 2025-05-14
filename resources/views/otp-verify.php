<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <style>
    .resend-otp-container {
        margin-top: 15px;
        text-align: center;
    }

    .resend-otp-button {
        background: none;
        border: none;
        color: blue;
        cursor: pointer;
        text-decoration: underline;
        padding: 0;
        margin: 0;
        font-size: 0.9rem;
    }

    .resend-otp-button:hover {
        color: darkblue;
    }

    .resend-otp-timer {
        font-size: 0.9rem;
        color: gray;
        margin-left: 5px;
    }
    </style>
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4" style="max-width: 500px; width: 100%; border-radius: 15px;">
            <h3 class="text-center mb-4">Verify OTP</h3>

            <form method="POST" action="verify-otp" id="otpForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email"
                           required
                           value="<?php echo isset($_SESSION['otp_email']) ? htmlspecialchars($_SESSION['otp_email']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="otp" class="form-label">Enter OTP</label>
                    <input type="text" name="otp" class="form-control" id="otp" required>
                </div>
                <div class="alert alert-danger mt-3" id="otpErrorAlert" style="display: none;">
                    Invalid or expired OTP!
                </div>
                <button type="submit" class="btn btn-success w-100">Verify OTP</button>
            </form>

            <!-- <div class="resend-otp-container mt-3 text-center">
                <button type="button" id="resendOtpBtn" class="btn btn-link p-0" onclick="resendOtp()">
                    Resend OTP
                </button>
                <span id="resendTimer" class="ms-2" style="display: none;">(Wait <span
                        id="countdown"></span>s)</span>
                <p id="resendMessage" class="mt-2 text-success" style="display: none;">New OTP sent!</p>
                <p id="resendError" class="mt-2 text-danger" style="display: none;"></p>
            </div> -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    let countdownInterval;
    let remainingTime = 0; // Initialize to 0
    const otpErrorAlert = document.getElementById('otpErrorAlert');
    const resendBtn = document.getElementById('resendOtpBtn');
    const resendTimerSpan = document.getElementById('resendTimer');
    const countdownDisplay = document.getElementById('countdown');
    const resendMessage = document.getElementById('resendMessage');
    const resendError = document.getElementById('resendError');

    function startResendTimer() {
        resendBtn.disabled = true;
        remainingTime = 60;
        countdownDisplay.textContent = remainingTime;
        resendTimerSpan.style.display = 'inline';

        countdownInterval = setInterval(function() {
            remainingTime--;
            countdownDisplay.textContent = remainingTime;

            if (remainingTime <= 0) {
                clearInterval(countdownInterval);
                resendBtn.disabled = false;
                resendTimerSpan.style.display = 'none';
            }
        }, 1000);
    }

    function resendOtp() {
        const email = document.getElementById('email').value;

        fetch('/resend-otp', { // Replace '/resend-otp' with your actual resend OTP route
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `email=${encodeURIComponent(email)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resendMessage.style.display = 'block';
                resendError.style.display = 'none';
                startResendTimer(); // Start the cooldown timer **here**
            } else {
                resendMessage.style.display = 'none';
                resendError.textContent = data.message || 'Failed to resend OTP. Please try again.';
                resendError.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error resending OTP:', error);
            resendMessage.style.display = 'none';
            resendError.textContent = 'An error occurred while resending OTP.';
            resendError.style.display = 'block';
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const otpForm = document.getElementById('otpForm');

        // Initially enable the resend button
        resendBtn.disabled = false;
        resendTimerSpan.style.display = 'none'; // Ensure timer is hidden initially

        otpForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(otpForm);
            const email = formData.get('email');
            const otp = formData.get('otp');

            fetch('/verify-otp', { // Replace '/verify-otp' with your actual verification route
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}&otp=${encodeURIComponent(otp)}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes("Invalid or expired OTP!")) {
                    otpErrorAlert.style.display = 'block';
                    // The resend button is always enabled now, so no need to enable it here
                } else if (data.includes("Database Error:")) {
                    console.error("OTP Verification Error:", data);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again later.',
                    });
                } else {
                    window.location.href = '/'; // Or your desired success URL
                }
            })
            .catch(error => {
                console.error('Error verifying OTP:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred during verification. Please try again.',
                });
            });
        });
    });
    </script>

    <?php if (isset($_SESSION['message'])): ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "<?php echo addslashes($_SESSION['message']); ?>",
            confirmButtonText: 'OK'
        });
    });
    </script>
    <?php unset($_SESSION['message']); // Clear the session message ?>
    <?php endif; ?>
</body>

</html>