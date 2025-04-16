<?php
session_start();
require_once 'includes/db_connect.php';

// Exchange rate: 1 USD = 75 INR
$exchangeRate = 75;

// Get category filter
$category = isset($_GET['category']) ? $_GET['category'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 10000 * $exchangeRate; // Convert to INR

// Build query
$sql = "SELECT p.*, c.name as category_name FROM packages p 
       LEFT JOIN categories c ON p.category_id = c.id 
       WHERE 1=1";

$params = [];
$types = "";

if (!empty($category)) {
   $sql .= " AND c.slug = ?";
   $params[] = $category;
   $types .= "s";
}

if (!empty($location)) {
   $sql .= " AND p.location LIKE ?";
   $params[] = "%$location%";
   $types .= "s";
}

// Convert min/max price from INR to USD for database query
$minPriceUsd = $minPrice / $exchangeRate;
$maxPriceUsd = $maxPrice / $exchangeRate;

$sql .= " AND p.price BETWEEN ? AND ?";
$params[] = $minPriceUsd;
$params[] = $maxPriceUsd;
$types .= "dd"; // Use double for decimal values

// Add sorting
switch (isset($_GET['sort']) ? $_GET['sort'] : 'price_asc') {
   case 'price_desc':
       $sql .= " ORDER BY p.price DESC";
       break;
   case 'rating_desc':
       $sql .= " ORDER BY p.rating DESC";
       break;
   case 'price_asc':
   default:
       $sql .= " ORDER BY p.price ASC";
       break;
}

// Prepare and execute query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
   $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$packages = [];

if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
       $packages[] = $row;
   }
}

// Get all categories for filter
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
$categories = [];

if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
       $categories[] = $row;
   }
}

// Get min and max prices for filter
$sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM packages";
$result = $conn->query($sql);
$priceRange = $result->fetch_assoc();
$dbMinPrice = ($priceRange['min_price'] ?? 0) * $exchangeRate; // Convert to INR
$dbMaxPrice = ($priceRange['max_price'] ?? 10000) * $exchangeRate; // Convert to INR

// If no min/max price is set in the request, use the database values
if (!isset($_GET['min_price'])) $minPrice = $dbMinPrice;
if (!isset($_GET['max_price'])) $maxPrice = $dbMaxPrice;

// Save search to history if user is logged in and search term is provided
if (isset($_SESSION['user_id']) && !empty($location)) {
    $userId = $_SESSION['user_id'];
    $searchTerm = $location;
    
    $sql = "INSERT INTO search_history (user_id, search_term, search_date) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $userId, $searchTerm);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Travel Packages - My Trip</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
   <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
   <?php include 'includes/header.php'; ?>
   
   <div class="container py-5">
       <h1 class="mb-4">Travel Packages</h1>
       
       <!-- Filter Section -->
       <div class="card mb-4">
           <div class="card-body">
               <form action="packages.php" method="GET" class="row g-3">
                   <!-- Search by location -->
                   <div class="col-md-4">
                       <label for="location" class="form-label">Destination</label>
                       <input type="text" class="form-control" id="location" name="location" placeholder="Search destinations..." value="<?php echo htmlspecialchars($location); ?>">
                   </div>
                   
                   <!-- Category filter -->
                   <div class="col-md-3">
                       <label for="category" class="form-label">Category</label>
                       <select class="form-select" id="category" name="category">
                           <option value="">All Categories</option>
                           <?php foreach ($categories as $cat): ?>
                               <option value="<?php echo $cat['slug']; ?>" <?php echo ($category == $cat['slug']) ? 'selected' : ''; ?>>
                                   <?php echo $cat['name']; ?>
                               </option>
                           <?php endforeach; ?>
                       </select>
                   </div>
                   
                   <!-- Sort by -->
                   <div class="col-md-3">
                       <label for="sort" class="form-label">Sort By</label>
                       <select class="form-select" id="sort" name="sort">
                           <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                           <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                           <option value="rating_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating_desc') ? 'selected' : ''; ?>>Highest Rated</option>
                       </select>
                   </div>
                   
                   <div class="col-md-2 d-flex align-items-end">
                       <button type="submit" class="btn btn-primary w-100">Filter</button>
                   </div>
                   
                   <!-- Price range -->
                   <div class="col-12">
                       <label for="price-range" class="form-label">Price Range: ₹<?php echo number_format($minPrice); ?> - ₹<?php echo number_format($maxPrice); ?></label>
                       <div class="row">
                           <div class="col-6">
                               <input type="range" class="form-range" id="min-price" name="min_price" min="<?php echo $dbMinPrice; ?>" max="<?php echo $dbMaxPrice; ?>" value="<?php echo $minPrice; ?>">
                           </div>
                           <div class="col-6">
                               <input type="range" class="form-range" id="max-price" name="max_price" min="<?php echo $dbMinPrice; ?>" max="<?php echo $dbMaxPrice; ?>" value="<?php echo $maxPrice; ?>">
                           </div>
                       </div>
                   </div>
               </form>
           </div>
       </div>
       
       <!-- Packages Grid -->
       <div class="row g-4">
           <?php if (empty($packages)): ?>
               <div class="col-12 text-center py-5">
                   <h3>No packages found</h3>
                   <p class="text-muted">Try adjusting your search criteria</p>
               </div>
           <?php else: ?>
               <?php foreach ($packages as $package): ?>
                   <div class="col-md-4">
                       <div class="card package-card h-100">
                           <img src="<?php echo $package['image']; ?>" class="card-img-top" alt="<?php echo $package['title']; ?>">
                           <div class="card-body">
                               <h3 class="card-title"><?php echo $package['title']; ?></h3>
                               <p class="card-text"><?php echo $package['description']; ?></p>
                               <div class="d-flex align-items-center text-muted mb-3">
                                   <i class="bi bi-geo-alt me-2"></i>
                                   <span><?php echo $package['location']; ?></span>
                               </div>
                               <div class="d-flex justify-content-between align-items-center">
                                   <span class="fs-5 fw-bold text-primary">₹<?php echo number_format($package['price'] * $exchangeRate); ?></span>
                                   <div class="rating">
                                       <i class="bi bi-star-fill text-warning"></i>
                                       <span><?php echo $package['rating']; ?></span>
                                   </div>
                               </div>
                               <div class="mt-2">
                                   <span class="badge bg-light text-dark"><?php echo $package['category_name']; ?></span>
                               </div>
                           </div>
                           <div class="card-footer bg-white border-top-0">
                               <a href="package-details.php?id=<?php echo $package['id']; ?>" class="btn btn-primary w-100">View Details</a>
                           </div>
                       </div>
                   </div>
               <?php endforeach; ?>
           <?php endif; ?>
       </div>
   </div>
   
   <?php include 'includes/footer.php'; ?>
   
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <script>
       // JavaScript for the price range slider
       const minPriceInput = document.getElementById('min-price');
       const maxPriceInput = document.getElementById('max-price');
       const priceRangeLabel = document.querySelector('label[for="price-range"]');
       
       function updatePriceRangeLabel() {
           priceRangeLabel.textContent = `Price Range: ₹${Number(minPriceInput.value).toLocaleString()} - ₹${Number(maxPriceInput.value).toLocaleString()}`;
       }
       
       minPriceInput.addEventListener('input', updatePriceRangeLabel);
       maxPriceInput.addEventListener('input', updatePriceRangeLabel);
   </script>
</body>
</html>

