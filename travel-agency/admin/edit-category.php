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

// Check if category ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: categories.php");
    exit();
}

$categoryId = $_GET['id'];

// Get category details
$sql = "SELECT * FROM categories WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: categories.php");
    exit();
}

$category = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $slug = strtolower(str_replace(' ', '-', $name));
    $description = $_POST['description'];
    
    // Check if a new image is uploaded
    if ($_FILES["image"]["size"] > 0) {
        // Handle image upload
        $targetDir = "../assets/images/categories/";
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
                $imagePath = "assets/images/categories/" . $imageName;
                
                // Update category with new image
                $sql = "UPDATE categories SET name = ?, slug = ?, description = ?, image = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $name, $slug, $description, $imagePath, $categoryId);
            } else {
                $errorMessage = "Error uploading image.";
            }
        } else {
            $errorMessage = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    } else {
        // Update category without changing the image
        $sql = "UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $slug, $description, $categoryId);
    }
    
    // Execute the update query if no error occurred
    if (empty($errorMessage)) {
        // Check if slug already exists for other categories
        $sql = "SELECT id FROM categories WHERE slug = ? AND id != ?";
        $checkStmt = $conn->prepare($sql);
        $checkStmt->bind_param("si", $slug, $categoryId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $errorMessage = "A category with this name already exists. Please choose a different name.";
        } else {
            if ($stmt->execute()) {
                $successMessage = "Category updated successfully!";
                
                // Refresh category data
                $sql = "SELECT * FROM categories WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $categoryId);
                $stmt->execute();
                $result = $stmt->get_result();
                $category = $result->fetch_assoc();
            } else {
                $errorMessage = "Error updating category: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category - My Trip Admin</title>
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
                    <h1 class="h2">Edit Category</h1>
                    <a href="categories.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Categories
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
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $categoryId); ?>" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                                <div class="form-text">The slug will be automatically generated from the name.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Category Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Leave empty to keep the current image. Recommended size: 800x600 pixels</div>
                                <?php if (!empty($category['image'])): ?>
                                    <div class="mt-2">
                                        <p>Current Image:</p>
                                        <img src="../<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="categories.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Category</button>
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

