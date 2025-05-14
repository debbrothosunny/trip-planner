<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken() {
        return bin2hex(random_bytes(32));
    }
}

function getCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateCsrfToken();
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            return false; // CSRF validation failed
        } else {
            // Regenerate the token after successful validation
            $_SESSION['csrf_token'] = generateCsrfToken();
            return true; // CSRF validation successful
        }
    }
    // For GET requests, CSRF validation is usually not needed
    return true;
}

function csrfInputField() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(getCsrfToken()) . '">';
}

