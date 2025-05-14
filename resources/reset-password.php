<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3.3.4/dist/vue.global.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reset-password-container {
            width: 100%;
            max-width: 400px;
            background: #0f3460;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 1.8em;
            color: #ffffff;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #1e3a8a;
            background: #16213e;
            color: #ffffff;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #00a8ff;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 168, 255, 0.6);
        }

        .btn-primary {
            background-color: #00a8ff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0080c6;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <div class="reset-password-container">
        <h2>Reset Your Password</h2>
        <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
        ?>
        <form action="update_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <input type="password" class="form-control" name="password" placeholder="New Password" required>
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit" class="btn-primary">Reset Password</button>
        </form>
    </div>
</body>
</html>