<?php
/**
 * MaintenancePro v2.0 - Logout
 * Destroy session and redirect to login
 */

require_once 'config.php';

if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'] ?? null;
    logActivity('LOGOUT', 'USER', $user_id, 'User logged out');
}

// Destroy session
session_destroy();

// Redirect to login
header("Location: login.php");
exit;
?>
