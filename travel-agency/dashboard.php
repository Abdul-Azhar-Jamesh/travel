<?php
session_start();
require_once 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get user bookings
$sql = "SELECT b.*, p.title, p.image, p.location
        FROM bookings b
        JOIN packages p ON b.package_id = p.id
        WHERE b.user_id = ?
        ORDER BY b.booking_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Get user search history
$sql = "SELECT * FROM search_history WHERE user_id = ? ORDER BY search_date DESC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$searchHistory = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $searchHistory[] = $row;
    }
}

// Exchange rate: 1 USD = 75 INR
$exchangeRate = 75;

// Dashboard stats
$totalBookings = count($bookings);
$upcomingTrips = 0;
$totalSpent = 0;

foreach ($bookings as $booking) {
    if (strtotime($booking['travel_date']) > time() && $booking['status'] !== 'Cancelled') {
        $upcomingTrips++;
    }
    $totalSpent += $booking['total_amount'];
}

// Convert total spent to INR
$totalSpentInr = round($totalSpent * $exchangeRate);

// Handle clear search history
if (isset($_POST['clear_history'])) {
    $sql = "DELETE FROM search_history WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    // Redirect to refresh page
    header("Location: dashboard.php");
    exit();
}

// Active tab management
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - My Trip</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid py-5">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-lg-3 col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="avatar mb-3">
                                <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        
                        <nav class="nav flex-column nav-pills">
                            <a class="nav-link <?php echo ($activeTab == 'dashboard') ? 'active' : ''; ?>" href="dashboard.php?tab=dashboard">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                            <a class="nav-link <?php echo ($activeTab == 'bookings') ? 'active' : ''; ?>" href="dashboard.php?tab=bookings">
                                <i class="bi bi-calendar-check me-2"></i> My Bookings
                            </a>
                            <a class="nav-link <?php echo ($activeTab == 'history') ? 'active' : ''; ?>" href="dashboard.php?tab=history">
                                <i class="bi bi-clock-history me-2"></i> Search History
                            </a>
                            <a class="nav-link <?php echo ($activeTab == 'profile') ? 'active' : ''; ?>" href="dashboard.php?tab=profile">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                            <a class="nav-link <?php echo ($activeTab == 'settings') ? 'active' : ''; ?>" href="dashboard.php?tab=settings">
                                <i class="bi bi-gear me-2"></i> Settings
                            </a>
                            <hr>
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </nav>
                    </div>
                </div>
            </aside>
            
            <!-- Main Content -->
            <main class="col-lg-9 col-md-8">
                <?php if ($activeTab == 'dashboard'): ?>
                    <div class="mb-4">
                        <h2 class="mb-0">Dashboard</h2>
                        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?></p>
                    </div>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Total Bookings</h6>
                                            <h2 class="mb-0"><?php echo $totalBookings; ?></h2>
                                        </div>
                                        <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Upcoming Trips</h6>
                                            <h2 class="mb-0"><?php echo $upcomingTrips; ?></h2>
                                        </div>
                                        <i class="bi bi-airplane fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Total Spent</h6>
                                            <h2 class="mb-0">₹<?php echo number_format($totalSpentInr); ?></h2>
                                        </div>
                                        <i class="bi bi-wallet fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0">Upcoming Trips</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($bookings) || $upcomingTrips == 0): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">You don't have any upcoming trips.</p>
                                    <a href="packages.php" class="btn btn-primary mt-3">Browse Packages</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <?php if (strtotime($booking['travel_date']) > time() && $booking['status'] !== 'Cancelled'): ?>
                                        <div class="d-flex align-items-center py-3 border-bottom">
                                            <img src="<?php echo $booking['image']; ?>" alt="<?php echo htmlspecialchars($booking['title']); ?>" class="rounded" style="width: 80px; height: 60px; object-fit: cover;">
                                            <div class="ms-3 flex-grow-1">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($booking['title']); ?></h6>
                                                <div class="d-flex align-items-center text-muted small">
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    <span><?php echo htmlspecialchars($booking['location']); ?></span>
                                                </div>
                                                <div class="d-flex align-items-center text-muted small mt-1">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    <span><?php echo date('d M Y', strtotime($booking['travel_date'])); ?></span>
                                                </div>
                                            </div>
                                            <div class="ms-3 text-end">
                                                <span class="badge bg-<?php echo ($booking['status'] == 'Confirmed') ? 'success' : 'warning'; ?> mb-2">
                                                    <?php echo $booking['status']; ?>
                                                </span>
                                                <div>₹<?php echo number_format($booking['total_amount'] * $exchangeRate); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php elseif ($activeTab == 'bookings'): ?>
                    <div class="mb-4">
                        <h2 class="mb-0">My Bookings</h2>
                        <p class="text-muted">View and manage your bookings</p>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <?php if (empty($bookings)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">You don't have any bookings yet.</p>
                                    <a href="packages.php" class="btn btn-primary mt-3">Browse Packages</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Booking ID</th>
                                                <th>Package</th>
                                                <th>Travel Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($bookings as $booking): ?>
                                                <tr>
                                                    <td>#<?php echo $booking['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($booking['title']); ?></td>
                                                    <td><?php echo date('d M Y', strtotime($booking['travel_date'])); ?></td>
                                                    <td>₹<?php echo number_format($booking['total_amount'] * $exchangeRate); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($booking['status'] == 'Confirmed') ? 'success' : (($booking['status'] == 'Pending') ? 'warning' : 'danger'); ?>">
                                                            <?php echo $booking['status']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bookingModal<?php echo $booking['id']; ?>">
                                                            View Details
                                                        </button>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Booking Details Modal -->
                                                <div class="modal fade" id="bookingModal<?php echo $booking['id']; ?>" tabindex="-1" aria-labelledby="bookingModalLabel<?php echo $booking['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="bookingModalLabel<?php echo $booking['id']; ?>">Booking Details #<?php echo $booking['id']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row mb-4">
                                                                    <div class="col-md-4">
                                                                        <img src="<?php echo $booking['image']; ?>" alt="<?php echo htmlspecialchars($booking['title']); ?>" class="img-fluid rounded">
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <h4><?php echo htmlspecialchars($booking['title']); ?></h4>
                                                                        <div class="d-flex align-items-center text-muted mb-2">
                                                                            <i class="bi bi-geo-alt me-2"></i>
                                                                            <span><?php echo htmlspecialchars($booking['location']); ?></span>
                                                                        </div>
                                                                        <p><strong>Status:</strong> 
                                                                            <span class="badge bg-<?php echo ($booking['status'] == 'Confirmed') ? 'success' : (($booking['status'] == 'Pending') ? 'warning' : 'danger'); ?>">
                                                                                <?php echo $booking['status']; ?>
                                                                            </span>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <p><strong>Booking Date:</strong> <?php echo date('d M Y', strtotime($booking['booking_date'])); ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Travel Date:</strong> <?php echo date('d M Y', strtotime($booking['travel_date'])); ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Adults:</strong> <?php echo $booking['adults']; ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Children:</strong> <?php echo $booking['children']; ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($booking['payment_method']); ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Payment Status:</strong> <?php echo $booking['payment_status']; ?></p>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <p><strong>Notes:</strong> <?php echo !empty($booking['notes']) ? htmlspecialchars($booking['notes']) : 'None'; ?></p>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <h5>Total Amount: ₹<?php echo number_format($booking['total_amount'] * $exchangeRate); ?></h5>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <?php if ($booking['status'] == 'Pending' && strtotime($booking['travel_date']) > time()): ?>
                                                                    <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel Booking</a>
                                                                <?php endif; ?>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php elseif ($activeTab == 'history'): ?>
                    <div class="mb-4">
                        <h2 class="mb-0">Search History</h2>
                        <p class="text-muted">View your recent searches</p>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <?php if (empty($searchHistory)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">No search history found.</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group mb-3">
                                    <?php foreach ($searchHistory as $search): ?>
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($search['search_term']); ?></h6>
                                                <small class="text-muted"><?php echo date('d M Y, h:i A', strtotime($search['search_date'])); ?></small>
                                            </div>
                                            <a href="packages.php?search=<?php echo urlencode($search['search_term']); ?>" class="btn btn-sm btn-outline-primary">Search Again</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?tab=history"); ?>">
                                    <div class="d-grid">
                                        <button type="submit" name="clear_history" class="btn btn-outline-danger">Clear History</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php elseif ($activeTab == 'profile'): ?>
                    <div class="mb-4">
                        <h2 class="mb-0">My Profile</h2>
                        <p class="text-muted">View and update your profile information</p>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form action="update-profile.php" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                        <div class="form-text">Email cannot be changed</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Update Profile</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($activeTab == 'settings'): ?>
                    <div class="mb-4">
                        <h2 class="mb-0">Account Settings</h2>
                        <p class="text-muted">Manage your account preferences</p>
                    </div>
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form action="change-password.php" method="POST">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0">Delete Account</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">This action is irreversible. Once you delete your account, all your data will be permanently removed.</p>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Delete Account</button>
                            
                            <!-- Delete Account Modal -->
                            <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteAccountModalLabel">Confirm Account Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                                            <form action="delete-account.php" method="POST">
                                                <div class="mb-3">
                                                    <label for="delete_password" class="form-label">Enter your password to confirm</label>
                                                    <input type="password" class="form-control" id="delete_password" name="password" required>
                                                </div>
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-danger">Permanently Delete Account</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

