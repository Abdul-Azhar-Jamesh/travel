<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
   header("Location: ../login.php");
   exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['feedback_id']) || !isset($_POST['email']) || !isset($_POST['subject']) || !isset($_POST['message'])) {
   header("Location: feedback.php");
   exit();
}

$feedbackId = $_POST['feedback_id'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// Update feedback status to "Resolved" if it exists
$sql = "UPDATE feedback SET status = 'Resolved' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $feedbackId);
$stmt->execute();

// In a real application, you would send an email here
// For demonstration purposes, we'll just simulate it
$emailSent = true;

// Log the response in the database (optional)
$sql = "INSERT INTO feedback_responses (feedback_id, admin_id, response, created_at) 
        VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$adminId = $_SESSION['user_id'];
$stmt->bind_param("iis", $feedbackId, $adminId, $message);
$stmt->execute();

// Set success message and redirect
if ($emailSent) {
    $_SESSION['success_message'] = "Response sent successfully to " . $email;
} else {
    $_SESSION['error_message'] = "Failed to send response. Please try again.";
}

header("Location: view-feedback.php?id=" . $feedbackId);
exit();
?>

