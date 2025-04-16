<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Start session
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    // Set response code - 401 Unauthorized
    http_response_code(401);
    
    // Tell the user
    echo json_encode(array("message" => "User not logged in."));
    exit;
}

// Clear search history
$_SESSION['search_history'] = array();

// Set response code - 200 OK
http_response_code(200);

// Tell the user
echo json_encode(array("message" => "Search history cleared."));
?>