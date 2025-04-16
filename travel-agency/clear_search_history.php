<?php
// Start session
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Clear search history
$_SESSION['search_history'] = array();

// Redirect back to dashboard
header("Location: dashboard.php");
exit;
?>