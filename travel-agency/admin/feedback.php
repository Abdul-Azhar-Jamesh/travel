<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle feedback deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM feedback WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $successMessage = "Feedback deleted successfully!";
    } else {
        $errorMessage = "Error deleting feedback: " . $conn->error;
    }
}

// Get all feedback
$sql = "SELECT f.*, u.first_name, u.last_name 
        FROM feedback f 
        LEFT JOIN users u ON f.user_id = u.id 
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);
$feedbacks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback - My Trip Admin</title>
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
                    <h1 class="h2">Manage Feedback</h1>
                </div>
                
                <?php if (isset($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $successMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $errorMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Rating</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($feedbacks)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No feedback found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($feedbacks as $feedback): ?>
                                            <tr>
                                                <td><?php echo $feedback['id']; ?></td>
                                                <td>
                                                    <?php 
                                                    if ($feedback['user_id']) {
                                                        echo $feedback['first_name'] . ' ' . $feedback['last_name'];
                                                    } else {
                                                        echo $feedback['name'] . '<br><small class="text-muted">' . $feedback['email'] . '</small>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo $feedback['subject']; ?></td>
                                                <td>
                                                    <?php 
                                                    // Truncate message if too long
                                                    echo (strlen($feedback['message']) > 50) ? substr($feedback['message'], 0, 50) . '...' : $feedback['message']; 
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= $feedback['rating']) {
                                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                                        } else {
                                                            echo '<i class="bi bi-star text-warning"></i>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($feedback['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="view-feedback.php?id=<?php echo $feedback['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="feedback.php?delete=<?php echo $feedback['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this feedback?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

