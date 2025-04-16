<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
   header("Location: ../login.php");
   exit();
}

// Check if feedback ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
   header("Location: feedback.php");
   exit();
}

$feedbackId = $_GET['id'];

// Get feedback details
$sql = "SELECT f.*, u.first_name, u.last_name, u.email as user_email 
        FROM feedback f 
        LEFT JOIN users u ON f.user_id = u.id 
        WHERE f.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $feedbackId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
   header("Location: feedback.php");
   exit();
}

$feedback = $result->fetch_assoc();

// Handle feedback status update (if implemented)
$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
   $status = $_POST['status'];
   
   $sql = "UPDATE feedback SET status = ? WHERE id = ?";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("si", $status, $feedbackId);
   
   if ($stmt->execute()) {
       $successMessage = "Feedback status updated successfully!";
       
       // Refresh feedback data
       $sql = "SELECT f.*, u.first_name, u.last_name, u.email as user_email 
               FROM feedback f 
               LEFT JOIN users u ON f.user_id = u.id 
               WHERE f.id = ?";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param("i", $feedbackId);
       $stmt->execute();
       $result = $stmt->get_result();
       $feedback = $result->fetch_assoc();
   } else {
       $errorMessage = "Error updating feedback status: " . $stmt->error;
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Feedback - My Trip Admin</title>
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
                   <h1 class="h2">View Feedback</h1>
                   <a href="feedback.php" class="btn btn-secondary">
                       <i class="bi bi-arrow-left"></i> Back to Feedback List
                   </a>
               </div>
               
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
               
               <div class="card border-0 shadow-sm mb-4">
                   <div class="card-header bg-white py-3">
                       <h5 class="card-title mb-0">Feedback Details</h5>
                   </div>
                   <div class="card-body">
                       <div class="row">
                           <div class="col-md-6">
                               <h6 class="text-muted mb-3">Basic Information</h6>
                               <table class="table table-borderless">
                                   <tr>
                                       <th style="width: 150px;">Feedback ID:</th>
                                       <td>#<?php echo $feedback['id']; ?></td>
                                   </tr>
                                   <tr>
                                       <th>Date:</th>
                                       <td><?php echo date('F d, Y h:i A', strtotime($feedback['created_at'])); ?></td>
                                   </tr>
                                   <tr>
                                       <th>Subject:</th>
                                       <td><?php echo htmlspecialchars($feedback['subject']); ?></td>
                                   </tr>
                                   <tr>
                                       <th>Rating:</th>
                                       <td>
                                           <?php 
                                           for ($i = 1; $i <= 5; $i++) {
                                               if ($i <= $feedback['rating']) {
                                                   echo '<i class="bi bi-star-fill text-warning"></i>';
                                               } else {
                                                   echo '<i class="bi bi-star text-warning"></i>';
                                               }
                                           }
                                           echo ' (' . $feedback['rating'] . '/5)';
                                           ?>
                                       </td>
                                   </tr>
                                   <?php if (isset($feedback['status'])): ?>
                                   <tr>
                                       <th>Status:</th>
                                       <td>
                                           <span class="badge bg-<?php echo $feedback['status'] == 'Resolved' ? 'success' : ($feedback['status'] == 'Pending' ? 'warning' : 'info'); ?>">
                                               <?php echo $feedback['status'] ?? 'New'; ?>
                                           </span>
                                       </td>
                                   </tr>
                                   <?php endif; ?>
                               </table>
                           </div>
                           <div class="col-md-6">
                               <h6 class="text-muted mb-3">User Information</h6>
                               <table class="table table-borderless">
                                   <?php if ($feedback['user_id']): ?>
                                       <tr>
                                           <th style="width: 150px;">User ID:</th>
                                           <td>#<?php echo $feedback['user_id']; ?></td>
                                       </tr>
                                       <tr>
                                           <th>Name:</th>
                                           <td><?php echo htmlspecialchars($feedback['first_name'] . ' ' . $feedback['last_name']); ?></td>
                                       </tr>
                                       <tr>
                                           <th>Email:</th>
                                           <td><?php echo htmlspecialchars($feedback['user_email']); ?></td>
                                       </tr>
                                       <tr>
                                           <th>Action:</th>
                                           <td>
                                               <a href="view-user.php?id=<?php echo $feedback['user_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                   View User Profile
                                               </a>
                                           </td>
                                       </tr>
                                   <?php else: ?>
                                       <tr>
                                           <th>Name:</th>
                                           <td><?php echo htmlspecialchars($feedback['name']); ?></td>
                                       </tr>
                                       <tr>
                                           <th>Email:</th>
                                           <td><?php echo htmlspecialchars($feedback['email']); ?></td>
                                       </tr>
                                       <tr>
                                           <th>User Type:</th>
                                           <td>Guest User</td>
                                       </tr>
                                   <?php endif; ?>
                               </table>
                           </div>
                       </div>
                       
                       <div class="mt-4">
                           <h6 class="text-muted mb-3">Feedback Message</h6>
                           <div class="card">
                               <div class="card-body bg-light">
                                   <p class="mb-0"><?php echo nl2br(htmlspecialchars($feedback['message'])); ?></p>
                               </div>
                           </div>
                       </div>
                       
                       <?php if (isset($feedback['status'])): ?>
                       <div class="mt-4">
                           <h6 class="text-muted mb-3">Update Status</h6>
                           <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $feedbackId); ?>" class="row g-3">
                               <div class="col-md-4">
                                   <select class="form-select" name="status">
                                       <option value="New" <?php echo ($feedback['status'] == 'New') ? 'selected' : ''; ?>>New</option>
                                       <option value="Pending" <?php echo ($feedback['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                       <option value="In Progress" <?php echo ($feedback['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                       <option value="Resolved" <?php echo ($feedback['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                                   </select>
                               </div>
                               <div class="col-md-4">
                                   <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                               </div>
                           </form>
                       </div>
                       <?php endif; ?>
                   </div>
                   <div class="card-footer bg-white">
                       <div class="d-flex justify-content-between">
                           <a href="feedback.php" class="btn btn-secondary">Back to Feedback List</a>
                           <a href="feedback.php?delete=<?php echo $feedback['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this feedback?')">
                               Delete Feedback
                           </a>
                       </div>
                   </div>
               </div>
               
               <!-- Admin Response Section (if implemented) -->
               <div class="card border-0 shadow-sm">
                   <div class="card-header bg-white py-3">
                       <h5 class="card-title mb-0">Send Response</h5>
                   </div>
                   <div class="card-body">
                       <form action="send-response.php" method="POST">
                           <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                           <input type="hidden" name="email" value="<?php echo htmlspecialchars($feedback['email'] ?? $feedback['user_email']); ?>">
                           
                           <div class="mb-3">
                               <label for="subject" class="form-label">Subject</label>
                               <input type="text" class="form-control" id="subject" name="subject" value="RE: <?php echo htmlspecialchars($feedback['subject']); ?>" required>
                           </div>
                           
                           <div class="mb-3">
                               <label for="message" class="form-label">Message</label>
                               <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                           </div>
                           
                           <button type="submit" class="btn btn-primary">Send Response</button>
                       </form>
                   </div>
               </div>
           </main>
       </div>
   </div>
   
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

