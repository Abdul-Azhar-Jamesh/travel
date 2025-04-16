<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
   header("Location: ../login.php");
   exit();
}

// Exchange rate: 1 USD = 75 INR
$exchangeRate = 75;

// Get counts for dashboard
$sql = "SELECT COUNT(*) as total FROM packages";
$result = $conn->query($sql);
$packageCount = $result->fetch_assoc()['total'];

$sql = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$result = $conn->query($sql);
$userCount = $result->fetch_assoc()['total'];

$sql = "SELECT COUNT(*) as total FROM bookings";
$result = $conn->query($sql);
$bookingCount = $result->fetch_assoc()['total'];

$sql = "SELECT COUNT(*) as total FROM feedback";
$result = $conn->query($sql);
$feedbackCount = $result->fetch_assoc()['total'];

// Get recent bookings
$sql = "SELECT b.*, u.first_name, u.last_name, p.title as package_title 
       FROM bookings b 
       JOIN users u ON b.user_id = u.id 
       JOIN packages p ON b.package_id = p.id 
       ORDER BY b.booking_date DESC LIMIT 5";
$result = $conn->query($sql);
$recentBookings = [];

if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
       $recentBookings[] = $row;
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Dashboard - My Trip</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
   <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
   <div class="container-fluid">
       <div class="row">
           <!-- Sidebar -->
           <?php include 'includes/sidebar.php'; ?>
           
           <!-- Main content -->
           <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
               <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                   <h1 class="h2">Dashboard</h1>
                   <div class="btn-toolbar mb-2 mb-md-0">
                       <div class="btn-group me-2">
                           <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                       </div>
                   </div>
               </div>
               
               <!-- Stats cards -->
               <div class="row g-4 mb-4">
                   <div class="col-md-3">
                       <div class="card text-white bg-primary">
                           <div class="card-body">
                               <div class="d-flex justify-content-between align-items-center">
                                   <div>
                                       <h6 class="card-title">Total Packages</h6>
                                       <h2 class="mb-0"><?php echo $packageCount; ?></h2>
                                   </div>
                                   <i class="bi bi-box-seam fs-1 opacity-50"></i>
                               </div>
                           </div>
                           <div class="card-footer d-flex align-items-center justify-content-between">
                               <a href="packages.php" class="text-white text-decoration-none">View Details</a>
                               <i class="bi bi-chevron-right text-white"></i>
                           </div>
                       </div>
                   </div>
                   
                   <div class="col-md-3">
                       <div class="card text-white bg-success">
                           <div class="card-body">
                               <div class="d-flex justify-content-between align-items-center">
                                   <div>
                                       <h6 class="card-title">Total Bookings</h6>
                                       <h2 class="mb-0"><?php echo $bookingCount; ?></h2>
                                   </div>
                                   <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                               </div>
                           </div>
                           <div class="card-footer d-flex align-items-center justify-content-between">
                               <a href="bookings.php" class="text-white text-decoration-none">View Details</a>
                               <i class="bi bi-chevron-right text-white"></i>
                           </div>
                       </div>
                   </div>
                   
                   <div class="col-md-3">
                       <div class="card text-white bg-info">
                           <div class="card-body">
                               <div class="d-flex justify-content-between align-items-center">
                                   <div>
                                       <h6 class="card-title">Total Users</h6>
                                       <h2 class="mb-0"><?php echo $userCount; ?></h2>
                                   </div>
                                   <i class="bi bi-people fs-1 opacity-50"></i>
                               </div>
                           </div>
                           <div class="card-footer d-flex align-items-center justify-content-between">
                               <a href="users.php" class="text-white text-decoration-none">View Details</a>
                               <i class="bi bi-chevron-right text-white"></i>
                           </div>
                       </div>
                   </div>
                   
                   <div class="col-md-3">
                       <div class="card text-white bg-warning">
                           <div class="card-body">
                               <div class="d-flex justify-content-between align-items-center">
                                   <div>
                                       <h6 class="card-title">Feedback</h6>
                                       <h2 class="mb-0"><?php echo $feedbackCount; ?></h2>
                                   </div>
                                   <i class="bi bi-chat-dots fs-1 opacity-50"></i>
                               </div>
                           </div>
                           <div class="card-footer d-flex align-items-center justify-content-between">
                               <a href="feedback.php" class="text-white text-decoration-none">View Details</a>
                               <i class="bi bi-chevron-right text-white"></i>
                           </div>
                       </div>
                   </div>
               </div>
               
               <!-- Recent bookings -->
               <div class="card mb-4">
                   <div class="card-header">
                       <h5 class="card-title mb-0">Recent Bookings</h5>
                   </div>
                   <div class="card-body">
                       <div class="table-responsive">
                           <table class="table table-striped table-hover">
                               <thead>
                                   <tr>
                                       <th>ID</th>
                                       <th>Customer</th>
                                       <th>Package</th>
                                       <th>Date</th>
                                       <th>Status</th>
                                       <th>Amount</th>
                                       <th>Actions</th>
                                   </tr>
                               </thead>
                               <tbody>
                                   <?php if (empty($recentBookings)): ?>
                                       <tr>
                                           <td colspan="7" class="text-center">No bookings found</td>
                                       </tr>
                                   <?php else: ?>
                                       <?php foreach ($recentBookings as $booking): ?>
                                           <tr>
                                               <td>#<?php echo $booking['id']; ?></td>
                                               <td><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></td>
                                               <td><?php echo $booking['package_title']; ?></td>
                                               <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                                               <td>
                                                   <span class="badge bg-<?php echo $booking['status'] == 'Confirmed' ? 'success' : ($booking['status'] == 'Pending' ? 'warning' : 'danger'); ?>">
                                                       <?php echo $booking['status']; ?>
                                                   </span>
                                               </td>
                                               <td>₹<?php echo number_format($booking['total_amount'] * $exchangeRate, 2); ?></td>
                                               <td>
                                                   <a href="booking-details.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-primary">
                                                       <i class="bi bi-eye"></i>
                                                   </a>
                                               </td>
                                           </tr>
                                       <?php endforeach; ?>
                                   <?php endif; ?>
                               </tbody>
                           </table>
                       </div>
                   </div>
                   <div class="card-footer text-end">
                       <a href="bookings.php" class="btn btn-primary btn-sm">View All Bookings</a>
                   </div>
               </div>
           </main>
       </div>
   </div>
   
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

