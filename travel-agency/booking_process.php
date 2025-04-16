<?php
// Start session
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database and required files
include_once 'config/database.php';
include_once 'classes/Booking.php';
include_once 'classes/Package.php';
include_once 'classes/DataStructures.php';

// Check if form is submitted
if($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: packages.php");
    exit;
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get form data
$package_id = $_POST['package_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$travelers = isset($_POST['travelers']) ? intval($_POST['travelers']) : 1;

// Validate input
if(empty($package_id) || empty($start_date) || empty($end_date)) {
    $_SESSION['booking_error'] = "Please fill in all required fields.";
    header("Location: package_details.php?id=" . $package_id);
    exit;
}

// Validate dates
$today = date('Y-m-d');
if($start_date < $today) {
    $_SESSION['booking_error'] = "Start date cannot be in the past.";
    header("Location: package_details.php?id=" . $package_id);
    exit;
}

if($end_date <= $start_date) {
    $_SESSION['booking_error'] = "End date must be after start date.";
    header("Location: package_details.php?id=" . $package_id);
    exit;
}

// Get package details
$package = new Package($db);
$package->setId($package_id);

if(!$package->read()) {
    $_SESSION['booking_error'] = "Package not found.";
    header("Location: packages.php");
    exit;
}

// Calculate total price
$start = new DateTime($start_date);
$end = new DateTime($end_date);
$days = $end->diff($start)->days;
$base_price = $package->getPrice();
$total_price = $base_price * ($days > 0 ? $days : 1) * $travelers;

// Create booking
$booking = new Booking($db);
$booking->setUserId($_SESSION['user_id']);
$booking->setPackageId($package_id);
$booking->setStartDate($start_date);
$booking->setEndDate($end_date);
$booking->setTotalPrice($total_price);

// Add booking to queue
$bookingQueue = new BookingQueue();

// Create booking object for queue
$bookingData = array(
    "user_id" => $_SESSION['user_id'],
    "package_id" => $package_id,
    "start_date" => $start_date,
    "end_date" => $end_date,
    "total_price" => $total_price,
    "timestamp" => date('Y-m-d H:i:s')
);

$bookingQueue->enqueue($bookingData);

// Process booking (in a real system, this might be done by a separate process)
$currentBooking = $bookingQueue->dequeue();

if($currentBooking) {
    // Create the booking
    if($booking->create()) {
        $_SESSION['booking_success'] = "Booking created successfully! Your booking is now pending confirmation.";
        header("Location: booking_confirmation.php?id=" . $db->lastInsertId());
        exit;
    } else {
        $_SESSION['booking_error'] = "Unable to create booking. Please try again.";
        header("Location: package_details.php?id=" . $package_id);
        exit;
    }
} else {
    $_SESSION['booking_error'] = "Error processing booking queue.";
    header("Location: package_details.php?id=" . $package_id);
    exit;
}
?>