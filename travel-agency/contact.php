<?php
session_start();
require_once 'includes/db_connect.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// If admin, redirect to admin dashboard
if ($isAdmin) {
    header("Location: admin/dashboard.php");
    exit();
}

$successMessage = '';
$errorMessage = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $errorMessage = "Please fill in all fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address";
    } else {
        // Insert feedback into database
        $sql = "INSERT INTO feedback (user_id, name, email, subject, message, rating, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        
        $userId = $isLoggedIn ? $_SESSION['user_id'] : null;
        $stmt->bind_param("issssi", $userId, $name, $email, $subject, $message, $rating);
        
        if ($stmt->execute()) {
            $successMessage = "Thank you for your feedback! We'll get back to you soon.";
            // Clear form data
            $name = $email = $subject = $message = '';
            $rating = 5;
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - My Trip</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">Contact Us</h1>
                    <p class="lead">Have questions or feedback? We'd love to hear from you!</p>
                </div>
                <div class="col-md-6">
                    <img src="assets/images/contact-hero.jpg" alt="Contact My Trip" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Information -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-geo-alt fs-1 text-primary"></i>
                            </div>
                            <h3 class="card-title">Our Location</h3>
                            <p class="card-text text-muted">123 Travel Street<br>New York, NY 10001<br>United States</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-envelope fs-1 text-primary"></i>
                            </div>
                            <h3 class="card-title">Email Us</h3>
                            <p class="card-text text-muted">info@mytrip.com<br>support@mytrip.com<br>bookings@mytrip.com</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="bi bi-telephone fs-1 text-primary"></i>
                            </div>
                            <h3 class="card-title">Call Us</h3>
                            <p class="card-text text-muted">+1 (555) 123-4567<br>+1 (555) 987-6543<br>Mon-Fri: 9am-6pm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Form -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow">
                        <div class="card-body p-5">
                            <h2 class="text-center mb-4">Send Us a Message</h2>
                            
                            <?php if (!empty($successMessage)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $successMessage; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($errorMessage)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $errorMessage; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Your Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Your Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject" value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="rating" class="form-label">Rate Your Experience (1-5)</label>
                                    <select class="form-select" id="rating" name="rating">
                                        <option value="5" <?php echo (isset($rating) && $rating == 5) ? 'selected' : ''; ?>>5 - Excellent</option>
                                        <option value="4" <?php echo (isset($rating) && $rating == 4) ? 'selected' : ''; ?>>4 - Very Good</option>
                                        <option value="3" <?php echo (isset($rating) && $rating == 3) ? 'selected' : ''; ?>>3 - Good</option>
                                        <option value="2" <?php echo (isset($rating) && $rating == 2) ? 'selected' : ''; ?>>2 - Fair</option>
                                        <option value="1" <?php echo (isset($rating) && $rating == 1) ? 'selected' : ''; ?>>1 - Poor</option>
                                    </select>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

