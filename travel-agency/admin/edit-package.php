<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$successMessage = '';
$errorMessage = '';

// Check if package ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: packages.php");
    exit();
}

$packageId = $_GET['id'];

// Get all categories for the dropdown
$sql = "SELECT * FROM categories ORDER BY name ASC";
$result = $conn->query($sql);
$categories = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Get package details
$sql = "SELECT * FROM packages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $packageId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: packages.php");
    exit();
}

$package = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $slug = strtolower(str_replace(' ', '-', $title));
    $description = $_POST['description'];
    $location = $_POST['location'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    $discountPrice = !empty($_POST['discount_price']) ? $_POST['discount_price'] : null;
    $categoryId = $_POST['category_id'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Check if a new image is uploaded
    if ($_FILES["image"]["size"] > 0) {
        // Handle image upload
        $targetDir = "../assets/images/packages/";
        $imageName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Check if image file is a valid image
        $validExtensions = array("jpg", "jpeg", "png", "gif");
        
        if (in_array($imageFileType, $validExtensions)) {
            // Upload file
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $imagePath = "assets/images/packages/" . $imageName;
                
                // Update package with new image
                $sql = "UPDATE packages SET title = ?, slug = ?, description = ?, location = ?, duration = ?, 
                        price = ?, discount_price = ?, image = ?, featured = ?, category_id = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssddsiis", $title, $slug, $description, $location, $duration, $price, $discountPrice, $imagePath, $featured, $categoryId, $packageId);
            } else {
                $errorMessage = "Error uploading image.";
            }
        } else {
            $errorMessage = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    } else {
        // Update package without changing the image
        $sql = "UPDATE packages SET title = ?, slug = ?, description = ?, location = ?, duration = ?, 
                price = ?, discount_price = ?, featured = ?, category_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssdsiis", $title, $slug, $description, $location, $duration, $price, $discountPrice, $featured, $categoryId, $packageId);
    }
    
    // Execute the update query if no error occurred
    if (empty($errorMessage)) {
        if ($stmt->execute()) {
            $successMessage = "Package updated successfully!";
            
            // Refresh package data
            $sql = "SELECT * FROM packages WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $packageId);
            $stmt->execute();
            $result = $stmt->get_result();
            $package = $result->fetch_assoc();
        } else {
            $errorMessage = "Error updating package: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package - My Trip Admin</title>
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
                    <h1 class="h2">Edit Package</h1>
                    <a href="packages.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Packages
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
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $packageId); ?>" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Package Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($package['title']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($package['location']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">Duration</label>
                                    <input type="text" class="form-control" id="duration" name="duration" value="<?php echo htmlspecialchars($package['duration']); ?>" placeholder="e.g. 7 days" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" <?php echo ($package['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo $package['price']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="discount_price" class="form-label">Discount Price ($) (Optional)</label>
                                    <input type="number" class="form-control" id="discount_price" name="discount_price" step="0.01" min="0" value="<?php echo $package['discount_price']; ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($package['description']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Package Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Leave empty to keep the current image. Recommended size: 800x600 pixels</div>
                                <?php if (!empty($package['image'])): ?>
                                    <div class="mt-2">
                                        <p>Current Image:</p>
                                        <img src="../<?php echo htmlspecialchars($package['image']); ?>" alt="<?php echo htmlspecialchars($package['title']); ?>" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured" <?php echo ($package['featured'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="featured">Featured Package</label>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="packages.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Package</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

