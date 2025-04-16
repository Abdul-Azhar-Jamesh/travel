<?php
session_start();
require_once 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if booking ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php?tab=bookings");
    exit();
}

$bookingId = $_GET['id'];
$userId = $_SESSION['user_id'];

// Verify booking belongs to the user
$sql = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Booking doesn't exist or doesn't belong to the user
    header("Location: dashboard.php?tab=bookings");
    exit();
}

$booking = $result->fetch_assoc();

// Check if booking can be cancelled (only pending bookings)
if ($booking['status'] !== 'Pending') {
    $_SESSION['error_message'] = "Only pending bookings can be cancelled.";
    header("Location: dashboard.php?tab=bookings");
    exit();
}

// Cancel booking
$sql = "UPDATE bookings SET status = 'Cancelled' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookingId);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Booking cancelled successfully.";
} else {
    $_SESSION['error_message'] = "Failed to cancel booking: " . $conn->error;
}

header("Location: dashboard.php?tab=bookings");
exit();

