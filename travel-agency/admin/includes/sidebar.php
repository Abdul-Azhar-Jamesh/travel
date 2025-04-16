<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="px-3 py-4 mb-3">
            <a href="dashboard.php" class="text-decoration-none">
                <h5 class="text-white">My Trip Admin</h5>
            </a>
            <div class="text-white-50 small">
                Welcome, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin'; ?>
            </div>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' || basename($_SERVER['PHP_SELF']) == 'add-package.php' || basename($_SERVER['PHP_SELF']) == 'edit-package.php' ? 'active' : ''; ?>" href="packages.php">
                    <i class="bi bi-box-seam me-2"></i>
                    Packages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>" href="bookings.php">
                    <i class="bi bi-calendar-check me-2"></i>
                    Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">
                    <i class="bi bi-people me-2"></i>
                    Customers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'feedback.php' ? 'active' : ''; ?>" href="feedback.php">
                    <i class="bi bi-chat-dots me-2"></i>
                    Feedback
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                    <i class="bi bi-tags me-2"></i>
                    Categories
                </a>
            </li>
        </ul>
        
        <hr class="text-white-50">
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="../index.php" target="_blank">
                    <i class="bi bi-house me-2"></i>
                    View Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="../logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

