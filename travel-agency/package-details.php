<?php
session_start();
require_once 'includes/db_connect.php';

// Check if id is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: packages.php");
    exit();
}

$packageId = $_GET['id'];

// Get package details
$sql = "SELECT p.*, c.name as category_name 
        FROM packages p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $packageId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: packages.php");
    exit();
}

$package = $result->fetch_assoc();

// Exchange rate: 1 USD = 75 INR
$exchangeRate = 75;
$priceInr = round($package['price'] * $exchangeRate);
$discountPriceInr = $package['discount_price'] ? round($package['discount_price'] * $exchangeRate) : null;

// Booking functionality
$bookingSuccess = false;
$bookingError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_now'])) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page
        header("Location: login.php?redirect=package-details.php?id=" . $packageId);
        exit();
    }
    
    // Get form data
    $userId = $_SESSION['user_id'];
    $travelDate = $_POST['travel_date'];
    $adults = (int)$_POST['adults'];
    $children = isset($_POST['children']) ? (int)$_POST['children'] : 0;
    $paymentMethod = $_POST['payment_method'];
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    
    // Calculate total amount
    $totalAmount = $package['discount_price'] ? $package['discount_price'] : $package['price'];
    $totalAmount = $totalAmount * $adults + ($totalAmount * 0.5 * $children);
    
    // Validate input
    if (empty($travelDate) || $adults < 1) {
        $bookingError = "Please fill in all required fields";
    } else {
        // Insert booking
        $sql = "INSERT INTO bookings (user_id, package_id, booking_date, travel_date, adults, children, total_amount, payment_method, payment_status, status, notes) 
                VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, 'Pending', 'Pending', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisidsss", $userId, $packageId, $travelDate, $adults, $children, $totalAmount, $paymentMethod, $notes);

        
        if ($stmt->execute()) {
            $bookingSuccess = true;
        } else {
            $bookingError = "Failed to create booking: " . $conn->error;
        }
    }
}

// Get related packages
$sql = "SELECT * FROM packages 
        WHERE category_id = ? AND id != ? 
        ORDER BY RAND() 
        LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $package['category_id'], $packageId);
$stmt->execute();
$relatedResult = $stmt->get_result();
$relatedPackages = [];

if ($relatedResult->num_rows > 0) {
    while ($row = $relatedResult->fetch_assoc()) {
        $relatedPackages[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($package['title']); ?> - My Trip</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container py-5">
        <?php if ($bookingSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">Booking Successful!</h4>
                <p>Your booking has been created successfully. You can view your booking details in your dashboard.</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($bookingError)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $bookingError; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Package Details -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <img src="<?php echo htmlspecialchars($package['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($package['title']); ?>" style="height: 400px; object-fit: cover;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h1 class="card-title mb-0"><?php echo htmlspecialchars($package['title']); ?></h1>
                            <div class="d-flex align-items-center bg-light px-3 py-1 rounded">
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                <span class="fw-bold"><?php echo $package['rating']; ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center text-muted mb-3">
                            <i class="bi bi-geo-alt me-2"></i>
                            <span><?php echo htmlspecialchars($package['location']); ?></span>
                        </div>
                        <div class="mb-4">
                            <span class="badge bg-info me-2"><?php echo htmlspecialchars($package['category_name']); ?></span>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($package['duration']); ?></span>
                        </div>
                        <h5>Description</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($package['description'])); ?></p>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">Package Highlights</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                    <span>Guided Tours</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                    <span>Hotel Accommodation</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                    <span>Meals Included</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                    <span>Airport Transfers</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                    <span>24/7 Customer Support</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                    <span>Local Guide</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">Itinerary</h4>
                        <div class="accordion" id="accordionItinerary">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Day 1: Arrival
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionItinerary">
                                    <div class="accordion-body">
                                        <p>Arrive at your destination. Transfer to hotel and check-in. Welcome dinner in the evening.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Day 2: Exploring
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionItinerary">
                                    <div class="accordion-body">
                                        <p>Breakfast at hotel. Full day guided tour of the main attractions. Lunch at a local restaurant. Evening at leisure.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        Day 3: Adventure Day
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionItinerary">
                                    <div class="accordion-body">
                                        <p>Breakfast at hotel. Adventure activities suited to your destination. Lunch provided. Evening cultural show with dinner.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                        Day 4: Relaxation
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionItinerary">
                                    <div class="accordion-body">
                                        <p>Breakfast at hotel. Free day to relax or explore on your own. Optional activities available.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                        Day 5: Departure
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionItinerary">
                                    <div class="accordion-body">
                                        <p>Breakfast at hotel. Check-out and transfer to airport/station for departure.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Booking Card -->
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px; z-index: 999;">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">Book This Package</h4>
                        
                        <div class="mb-4">
                            <h5 class="text-primary mb-2">₹<?php echo number_format($priceInr); ?></h5>
                            <?php if ($discountPriceInr): ?>
                                <p class="text-muted"><del>₹<?php echo number_format($discountPriceInr); ?></del></p>
                            <?php endif; ?>
                            <p class="text-muted small">per person</p>
                        </div>
                        
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $packageId); ?>">
                            <div class="mb-3">
                                <label for="travel_date" class="form-label">Travel Date</label>
                                <input type="date" class="form-control" id="travel_date" name="travel_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="adults" class="form-label">Adults</label>
                                    <select class="form-select" id="adults" name="adults" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="children" class="form-label">Children</label>
                                    <select class="form-select" id="children" name="children">
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Debit Card">Debit Card</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Special Requests (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="book_now" class="btn btn-primary">Book Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Packages -->
        <?php if (!empty($relatedPackages)): ?>
            <h3 class="mt-5 mb-4">Related Packages</h3>
            <div class="row g-4">
                <?php foreach ($relatedPackages as $relPkg): ?>
                    <div class="col-md-4">
                        <div class="card package-card h-100">
                            <img src="<?php echo $relPkg['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($relPkg['title']); ?>" class="card-img-top">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($relPkg['title']); ?></h3>
                                <p class="card-text"><?php echo htmlspecialchars($relPkg['description']); ?></p>
                                <div class="d-flex align-items-center text-muted mb-3">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <span><?php echo htmlspecialchars($relPkg['location']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold text-primary">₹<?php echo number_format($relPkg['price'] * $exchangeRate); ?></span>
                                    <div class="rating">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <span><?php echo $relPkg['rating']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <a href="package-details.php?id=<?php echo $relPkg['id']; ?>" class="btn btn-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

