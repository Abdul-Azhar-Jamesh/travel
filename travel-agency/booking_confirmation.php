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

// Check if booking ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$booking_id = $_GET['id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get booking details
$booking = new Booking($db);
$booking->setId($booking_id);
$booking_details = $booking->getBookingDetails();

// Check if booking exists and belongs to the current user
if(!$booking_details || $booking_details['user_id'] != $_SESSION['user_id']) {
    header("Location: dashboard.php");
    exit;
}

// Get success message if available
$success_msg = isset($_SESSION['booking_success']) ? $_SESSION['booking_success'] : "";
unset($_SESSION['booking_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - My Trip</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand fw-bold text-primary" href="index.php">My Trip</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="destinations.php">Destinations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="packages.php">Packages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                    </ul>
                    <div class="d-flex">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <?php echo $_SESSION['firstName']; ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item active" href="bookings.php">My Bookings</a></li>
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <?php if($_SESSION['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="admin/index.php">Admin Panel</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <?php if(!empty($success_msg)): ?>
                            <div class="alert alert-success mb-4" role="alert">
                                <?php echo $success_msg; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h1 class="mt-3">Booking Confirmed!</h1>
                            <p class="lead">Your booking has been received and is now pending confirmation.</p>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Booking Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Booking ID:</div>
                                    <div class="col-md-8">#<?php echo $booking_details['id']; ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Package:</div>
                                    <div class="col-md-8"><?php echo $booking_details['title']; ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Location:</div>
                                    <div class="col-md-8"><?php echo $booking_details['location']; ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Travel Dates:</div>
                                    <div class="col-md-8">
                                        <?php 
                                            echo date('M d, Y', strtotime($booking_details['start_date'])) . ' - ' . 
                                                 date('M d, Y', strtotime($booking_details['end_date']));
                                        ?>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Status:</div>
                                    <div class="col-md-8">
                                        <span class="badge <?php 
                                            echo $booking_details['status'] == 'Confirmed' ? 'bg-success' : 
                                                ($booking_details['status'] == 'Pending' ? 'bg-warning text-dark' : 'bg-danger');
                                        ?>">
                                            <?php echo $booking_details['status']; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 fw-bold">Total Price:</div>
                                    <div class="col-md-8">$<?php echo number_format($booking_details['total_price'], 2); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info" role="alert">
                            <h5 class="alert-heading">What's Next?</h5>
                            <p>Our team will review your booking and confirm it shortly. You will receive an email notification once your booking is confirmed.</p>
                            <hr>
                            <p class="mb-0">If you have any questions, please contact our customer support at <a href="mailto:support@mytrip.com">support@mytrip.com</a> or call us at +1-234-567-8900.</p>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="dashboard.php" class="btn btn-outline-primary">Go to Dashboard</a>
                            <a href="bookings.php" class="btn btn-primary">View All Bookings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <h3 class="fs-5 mb-3">My Trip</h3>
                    <p>Your trusted travel companion for discovering the world's most amazing destinations.</p>
                </div>
                <div class="col-md-3">
                    <h4 class="fs-6 mb-3">Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white-50">Home</a></li>
                        <li><a href="destinations.php" class="text-white-50">Destinations</a></li>
                        <li><a href="packages.php" class="text-white-50">Packages</a></li>
                        <li><a href="about.php" class="text-white-50">About Us</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h4 class="fs-6 mb-3">Support</h4>
                    <ul class="list-unstyled">
                        <li><a href="faq.php" class="text-white-50">FAQ</a></li>
                        <li><a href="contact.php" class="text-white-50">Contact Us</a></li>
                        <li><a href="terms.php" class="text-white-50">Terms & Conditions</a></li>
                        <li><a href="privacy.php" class="text-white-50">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h4 class="fs-6 mb-3">Connect With Us</h4>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white-50"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white-50"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-white-50"><i class="bi bi-twitter fs-5"></i></a>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-top border-secondary text-center text-white-50">
                <p>&copy; <?php echo date('Y'); ?> My Trip. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS and Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>