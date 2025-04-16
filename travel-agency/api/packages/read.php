<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database and required files
include_once '../../config/database.php';
include_once '../../classes/Package.php';
include_once '../../classes/DataStructures.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize package object
$package = new Package($db);

// Get query parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : 10000;
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query packages based on parameters
if(!empty($search)) {
    $stmt = $package->searchPackages($search);
    
    // Add search term to search history if user is logged in
    if(isset($_SESSION['user_id'])) {
        // Create search history object
        $searchHistory = new SearchHistoryStack();
        
        // Add search term with timestamp
        $searchTerm = array(
            "term" => $search,
            "timestamp" => date('Y-m-d H:i:s')
        );
        
        $searchHistory->push($searchTerm);
        
        // Store in session
        if(!isset($_SESSION['search_history'])) {
            $_SESSION['search_history'] = array();
        }
        
        $_SESSION['search_history'] = $searchHistory->getAll();
    }
} else if(!empty($category)) {
    $stmt = $package->filterByCategory($category);
} else if($min_price > 0 || $max_price < 10000) {
    $stmt = $package->filterByPriceRange($min_price, $max_price);
} else {
    $stmt = $package->getAllPackages();
}

// Check if any packages found
$num = $stmt->rowCount();

if($num > 0) {
    // Packages array
    $packages_arr = array();
    $packages_arr["records"] = array();
    
    // Retrieve all packages
    $packages = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        $package_item = array(
            "id" => $id,
            "title" => $title,
            "description" => $description,
            "location" => $location,
            "price" => $price,
            "rating" => $rating,
            "category" => $category,
            "image" => $image
        );
        
        array_push($packages, $package_item);
    }
    
    // Apply sorting if specified
    if(!empty($sort_by)) {
        $count = count($packages);
        
        if($sort_by == 'price_asc') {
            PackageSorter::quickSortByPrice($packages, 0, $count - 1, true);
        } else if($sort_by == 'price_desc') {
            PackageSorter::quickSortByPrice($packages, 0, $count - 1, false);
        } else if($sort_by == 'rating_desc') {
            PackageSorter::quickSortByRating($packages, 0, $count - 1, false);
        } else if($sort_by == 'rating_asc') {
            PackageSorter::quickSortByRating($packages, 0, $count - 1, true);
        }
    }
    
    $packages_arr["records"] = $packages;
    
    // Set response code - 200 OK
    http_response_code(200);
    
    // Show packages data in JSON format
    echo json_encode($packages_arr);
} else {
    // Set response code - 404 Not found
    http_response_code(404);
    
    // Tell the user no packages found
    echo json_encode(array("message" => "No packages found."));
}
?>