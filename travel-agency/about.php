<?php
session_start();

// Check if user is logged in before accessing session variables
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// If admin, redirect to admin dashboard
if ($isAdmin) {
   header("Location: admin/dashboard.php");
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About Us - My Trip</title>
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
                   <h1 class="display-4 fw-bold mb-4">About Us</h1>
                   <p class="lead">Learn more about My Trip and our mission to provide exceptional travel experiences.</p>
               </div>
               <div class="col-md-6">
                   <img src="assets/images/about-hero.jpg" alt="About My Trip" class="img-fluid rounded shadow">
               </div>
           </div>
       </div>
   </section>
   
   <!-- Our Story Section -->
   <section class="py-5">
       <div class="container">
           <div class="row">
               <div class="col-md-6 mb-4 mb-md-0">
                   <img src="assets/images/our-story.jpg" alt="Our Story" class="img-fluid rounded shadow">
               </div>
               <div class="col-md-6">
                   <h2 class="mb-4">Our Story</h2>
                   <p>Founded in 2010, My Trip has grown from a small local travel agency to a leading provider of travel experiences worldwide. Our journey began with a simple mission: to help people explore the world and create unforgettable memories.</p>
                   <p>Over the years, we've helped thousands of travelers discover new destinations, experience different cultures, and embark on adventures that have transformed their lives.</p>
                   <p>Today, with a team of 25+ travel experts, we continue to provide personalized service and curated travel packages that cater to the unique preferences of each client.</p>
               </div>
           </div>
       </div>
   </section>
   
   <!-- Mission & Vision Section -->
   <section class="py-5 bg-light">
       <div class="container">
           <div class="row">
               <div class="col-md-6 mb-4 mb-md-0">
                   <div class="card h-100 border-0 shadow-sm">
                       <div class="card-body p-4">
                           <div class="d-flex align-items-center mb-4">
                               <div class="bg-primary text-white rounded-circle p-3 me-3">
                                   <i class="bi bi-bullseye fs-4"></i>
                               </div>
                               <h3 class="mb-0">Our Mission</h3>
                           </div>
                           <p class="card-text">To provide unforgettable travel experiences that enrich lives and create lasting memories.</p>
                       </div>
                   </div>
               </div>
               <div class="col-md-6">
                   <div class="card h-100 border-0 shadow-sm">
                       <div class="card-body p-4">
                           <div class="d-flex align-items-center mb-4">
                               <div class="bg-primary text-white rounded-circle p-3 me-3">
                                   <i class="bi bi-eye fs-4"></i>
                               </div>
                               <h3 class="mb-0">Our Vision</h3>
                           </div>
                           <p class="card-text">To be the leading travel agency known for exceptional service and unique travel experiences.</p>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </section>
   
   <!-- Team Section -->
   <section class="py-5">
       <div class="container">
           <h2 class="text-center mb-5">Meet Our Team</h2>
           
           <div class="row g-4">
               <div class="col-md-4">
                   <div class="card border-0 shadow-sm">
                       <img src="assets/images/team-1.jpg" class="card-img-top" alt="John Doe">
                       <div class="card-body text-center">
                           <h5 class="card-title">John Doe</h5>
                           <p class="text-muted">CEO & Founder</p>
                           <p class="card-text">With over 15 years of experience in the travel industry, John leads our team with passion and vision.</p>
                           <div class="social-icons">
                               <a href="#" class="text-muted me-2"><i class="bi bi-linkedin"></i></a>
                               <a href="#" class="text-muted me-2"><i class="bi bi-twitter"></i></a>
                               <a href="#" class="text-muted"><i class="bi bi-envelope"></i></a>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="col-md-4">
                   <div class="card border-0 shadow-sm">
                       <img src="assets/images/team-2.jpg" class="card-img-top" alt="Jane Smith">
                       <div class="card-body text-center">
                           <h5 class="card-title">Jane Smith</h5>
                           <p class="text-muted">Travel Director</p>
                           <p class="card-text">Jane's extensive knowledge of global destinations helps us create unique and authentic travel experiences.</p>
                           <div class="social-icons">
                               <a href="#" class="text-muted me-2"><i class="bi bi-linkedin"></i></a>
                               <a href="#" class="text-muted me-2"><i class="bi bi-twitter"></i></a>
                               <a href="#" class="text-muted"><i class="bi bi-envelope"></i></a>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="col-md-4">
                   <div class="card border-0 shadow-sm">
                       <img src="assets/images/team-3.jpg" class="card-img-top" alt="Michael Johnson">
                       <div class="card-body text-center">
                           <h5 class="card-title">Michael Johnson</h5>
                           <p class="text-muted">Customer Experience Manager</p>
                           <p class="card-text">Michael ensures that every client receives personalized service and has an unforgettable travel experience.</p>
                           <div class="social-icons">
                               <a href="#" class="text-muted me-2"><i class="bi bi-linkedin"></i></a>
                               <a href="#" class="text-muted me-2"><i class="bi bi-twitter"></i></a>
                               <a href="#" class="text-muted"><i class="bi bi-envelope"></i></a>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </section>
   
   <!-- Testimonials Section -->
   <section class="py-5 bg-light">
       <div class="container">
           <h2 class="text-center mb-5">What Our Clients Say</h2>
           
           <div class="row g-4">
               <div class="col-md-4">
                   <div class="card border-0 shadow-sm h-100">
                       <div class="card-body p-4">
                           <div class="mb-3">
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                           </div>
                           <p class="card-text mb-4">"My Trip made our honeymoon absolutely perfect! Every detail was taken care of, and we could just relax and enjoy our time together. Highly recommended!"</p>
                           <div class="d-flex align-items-center">
                               <img src="assets/images/testimonial-1.jpg" alt="Sarah & David" class="rounded-circle me-3" width="50" height="50">
                               <div>
                                   <h6 class="mb-0">Sarah & David</h6>
                                   <small class="text-muted">Bali Paradise Package</small>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="col-md-4">
                   <div class="card border-0 shadow-sm h-100">
                       <div class="card-body p-4">
                           <div class="mb-3">
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                           </div>
                           <p class="card-text mb-4">"The European Adventure package exceeded all my expectations. The itinerary was perfectly balanced, and the local guides were knowledgeable and friendly. I'll definitely book with My Trip again!"</p>
                           <div class="d-flex align-items-center">
                               <img src="assets/images/testimonial-2.jpg" alt="Robert Johnson" class="rounded-circle me-3" width="50" height="50">
                               <div>
                                   <h6 class="mb-0">Robert Johnson</h6>
                                   <small class="text-muted">European Adventure Package</small>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="col-md-4">
                   <div class="card border-0 shadow-sm h-100">
                       <div class="card-body p-4">
                           <div class="mb-3">
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-fill text-warning"></i>
                               <i class="bi bi-star-half text-warning"></i>
                           </div>
                           <p class="card-text mb-4">"As a solo traveler, I was looking for a safe and engaging experience. My Trip delivered exactly that with the Tokyo Explorer package. The attention to detail and personalized recommendations made all the difference."</p>
                           <div class="d-flex align-items-center">
                               <img src="assets/images/testimonial-3.jpg" alt="Emily Chen" class="rounded-circle me-3" width="50" height="50">
                               <div>
                                   <h6 class="mb-0">Emily Chen</h6>
                                   <small class="text-muted">Tokyo Explorer Package</small>
                               </div>
                           </div>
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

