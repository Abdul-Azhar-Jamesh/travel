<?php
session_start();
require_once 'includes/db_connect.php';

// Exchange rate: 1 USD = 75 INR
$exchangeRate = 75;

// Fetch categories
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
$categories = [];

if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
       $categories[] = $row;
   }
}

// Fetch featured packages
$sql = "SELECT * FROM packages WHERE featured = 1 LIMIT 3";
$result = $conn->query($sql);
$featuredPackages = [];

if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
       $featuredPackages[] = $row;
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>My Trip - Travel Agency</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
   <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
   <?php include 'includes/header.php'; ?>
   
   <!-- Hero Section -->
   <section class="hero-section">
       <div class="container text-center">
           <h1 class="display-4 fw-bold mb-4">Discover Your Perfect Trip</h1>
           <p class="lead mb-5">Explore destinations worldwide and book your dream vacation with our easy-to-use travel management system.</p>
           
           
       </div>
   </section>
   
   <!-- Categories Section -->
   <section class="py-5 bg-light">
       <div class="container">
           <h2 class="text-center mb-5">Explore by Category</h2>
           
           <div class="row g-4">
               <?php if (empty($categories)): ?>
                   <!-- Default categories if database is empty -->
                   <div class="col-md-4">
                       <a href="packages.php?category=beach" class="text-decoration-none">
                           <div class="card category-card h-100">
                               <img src="assets/images/beach.jpg" class="card-img-top" alt="Beach Vacations">
                               <div class="card-body text-center">
                                   <h3 class="card-title">Beach Vacations</h3>
                                   <p class="card-text text-muted">Relax on beautiful beaches around the world</p>
                               </div>
                           </div>
                       </a>
                   </div>
                   <div class="col-md-4">
                       <a href="packages.php?category=adventure" class="text-decoration-none">
                           <div class="card category-card h-100">
                               <img src="assets/images/adventure.jpg" class="card-img-top" alt="Adventure Tours">
                               <div class="card-body text-center">
                                   <h3 class="card-title">Adventure Tours</h3>
                                   <p class="card-text text-muted">Thrilling experiences for the adventurous traveler</p>
                               </div>
                           </div>
                       </a>
                   </div>
                   <div class="col-md-4">
                       <a href="packages.php?category=city" class="text-decoration-none">
                           <div class="card category-card h-100">
                               <img src="assets/images/city.jpg" class="card-img-top" alt="City Breaks">
                               <div class="card-body text-center">
                                   <h3 class="card-title">City Breaks</h3>
                                   <p class="card-text text-muted">Explore the world's most exciting cities</p>
                               </div>
                           </div>
                       </a>
                   </div>
               <?php else: ?>
                   <?php foreach ($categories as $category): ?>
                       <div class="col-md-4">
                           <a href="packages.php?category=<?php echo $category['slug']; ?>" class="text-decoration-none">
                               <div class="card category-card h-100">
                                   <img src="<?php echo $category['image']; ?>" class="card-img-top" alt="<?php echo $category['name']; ?>">
                                   <div class="card-body text-center">
                                       <h3 class="card-title"><?php echo $category['name']; ?></h3>
                                       <p class="card-text text-muted"><?php echo $category['description']; ?></p>
                                   </div>
                               </div>
                           </a>
                       </div>
                   <?php endforeach; ?>
               <?php endif; ?>
           </div>
       </div>
   </section>
   
   <!-- Featured Packages -->
   <section class="py-5">
       <div class="container">
           <h2 class="text-center mb-5">Featured Packages</h2>
           
           <div class="row g-4">
               <?php if (empty($featuredPackages)): ?>
                   <!-- Default packages if database is empty -->
                   <div class="col-md-4">
                       <div class="card package-card h-100">
                           <img src="assets/images/bali.jpg" class="card-img-top" alt="Bali Paradise">
                           <div class="card-body">
                               <h3 class="card-title">Bali Paradise</h3>
                               <p class="card-text">7 days in tropical paradise</p>
                               <div class="d-flex justify-content-between align-items-center">
                                   <span class="fs-5 fw-bold text-primary">₹97,425</span>
                                   <div class="rating">
                                       <i class="bi bi-star-fill text-warning"></i>
                                       <span>4.8</span>
                                   </div>
                               </div>
                           </div>
                           <div class="card-footer bg-white border-top-0">
                               <a href="package-details.php?id=1" class="btn btn-primary w-100">View Details</a>
                           </div>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="card package-card h-100">
                           <img src="assets/images/europe.jpg" class="card-img-top" alt="European Adventure">
                           <div class="card-body">
                               <h3 class="card-title">European Adventure</h3>
                               <p class="card-text">10 days across 4 countries</p>
                               <div class="d-flex justify-content-between align-items-center">
                                   <span class="fs-5 fw-bold text-primary">₹187,425</span>
                                   <div class="rating">
                                       <i class="bi bi-star-fill text-warning"></i>
                                       <span>4.9</span>
                                   </div>
                               </div>
                           </div>
                           <div class="card-footer bg-white border-top-0">
                               <a href="package-details.php?id=2" class="btn btn-primary w-100">View Details</a>
                           </div>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="card package-card h-100">
                           <img src="assets/images/tokyo.jpg" class="card-img-top" alt="Tokyo Explorer">
                           <div class="card-body">
                               <h3 class="card-title">Tokyo Explorer</h3>
                               <p class="card-text">5 days in Japan's capital</p>
                               <div class="d-flex justify-content-between align-items-center">
                                   <span class="fs-5 fw-bold text-primary">₹142,425</span>
                                   <div class="rating">
                                       <i class="bi bi-star-fill text-warning"></i>
                                       <span>4.7</span>
                                   </div>
                               </div>
                           </div>
                           <div class="card-footer bg-white border-top-0">
                               <a href="package-details.php?id=3" class="btn btn-primary w-100">View Details</a>
                           </div>
                       </div>
                   </div>
               <?php else: ?>
                   <?php foreach ($featuredPackages as $package): ?>
                       <div class="col-md-4">
                           <div class="card package-card h-100">
                               <img src="<?php echo $package['image']; ?>" class="card-img-top" alt="<?php echo $package['title']; ?>">
                               <div class="card-body">
                                   <h3 class="card-title"><?php echo $package['title']; ?></h3>
                                   <p class="card-text"><?php echo $package['description']; ?></p>
                                   <div class="d-flex justify-content-between align-items-center">
                                       <span class="fs-5 fw-bold text-primary">₹<?php echo number_format($package['price'] * $exchangeRate); ?></span>
                                       <div class="rating">
                                           <i class="bi bi-star-fill text-warning"></i>
                                           <span><?php echo $package['rating']; ?></span>
                                       </div>
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
           
           <div class="text-center mt-5">
               <a href="packages.php" class="btn btn-outline-primary">View All Packages</a>
           </div>
       </div>
   </section>
   
   <!-- Features Section -->
   <section class="py-5 bg-light">
       <div class="container">
           <h2 class="text-center mb-5">Why Choose My Trip</h2>
           
           <div class="row g-4">
               <div class="col-md-4">
                   <div class="card h-100 border-0 shadow-sm">
                       <div class="card-body text-center p-4">
                           <div class="feature-icon mb-3">
                               <i class="bi bi-search fs-1 text-primary"></i>
                           </div>
                           <h3 class="card-title">Smart Search</h3>
                           <p class="card-text text-muted">Find your perfect destination with our advanced search algorithms.</p>
                       </div>
                   </div>
               </div>
               <div class="col-md-4">
                   <div class="card h-100 border-0 shadow-sm">
                       <div class="card-body text-center p-4">
                           <div class="feature-icon mb-3">
                               <i class="bi bi-graph-up-arrow fs-1 text-primary"></i>
                           </div>
                           <h3 class="card-title">Best Prices</h3>
                           <p class="card-text text-muted">We guarantee the best prices for all our travel packages.</p>
                       </div>
                   </div>
               </div>
               <div class="col-md-4">
                   <div class="card h-100 border-0 shadow-sm">
                       <div class="card-body text-center p-4">
                           <div class="feature-icon mb-3">
                               <i class="bi bi-person-check fs-1 text-primary"></i>
                           </div>
                           <h3 class="card-title">Personalized Experience</h3>
                           <p class="card-text text-muted">Get recommendations based on your preferences and past trips.</p>
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

