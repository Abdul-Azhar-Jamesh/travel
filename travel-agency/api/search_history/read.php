<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

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

// Check if search history exists in session
if(isset($_SESSION['search_history']) && !empty($_SESSION['search_history'])) {
    // Set response code - 200 OK
    http_response_code(200);
    
    // Return search history
    echo json_encode(array("records" => $_SESSION['search_history']));
} else {
    // Set response code - 404 Not found
    http_response_code(404);
    
    // Tell the user
    echo json_encode(array("message" => "No search history found."));
}
?>