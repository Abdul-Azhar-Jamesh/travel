<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and required files
include_once '../../config/database.php';
include_once '../../classes/Booking.php';
include_once '../../classes/Package.php';
include_once '../../classes/DataStructures.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Prepare booking object
$booking = new Booking($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Check if user is logged in
session_start();
if(!isset($_SESSION['user_id'])) {
    // Set response code - 401 Unauthorized
    http_response_code(401);
    
    // Tell the user
    echo json_encode(array("message" => "User not logged in."));
    exit;
}

// Make sure data is not empty
if(
    !empty($data->package_id) &&
    !empty($data->start_date) &&
    !empty($data->end_date)
) {
    // Get package details to calculate price
    $package = new Package($db);
    $package->setId($data->package_id);
    
    if($package->read()) {
        // Set booking property values
        $booking->setUserId($_SESSION['user_id']);
        $booking->setPackageId($data->package_id);
        $booking->setStartDate($data->start_date);
        $booking->setEndDate($data->end_date);
        
        // Calculate total price (package price * number of days)
        $start = new DateTime($data->start_date);
        $end = new DateTime($data->end_date);
        $days = $end->diff($start)->days;
        $total_price = $package->getPrice() * ($days > 0 ? $days : 1);
        
        $booking->setTotalPrice($total_price);
        
        // Add booking to queue
        $bookingQueue = new BookingQueue();
        
        // Create booking object for queue
        $bookingData = array(
            "user_id" => $_SESSION['user_id'],
            "package_id" => $data->package_id,
            "start_date" => $data->start_date,
            "end_date" => $data->end_date,
            "total_price" => $total_price,
            "timestamp" => date('Y-m-d H:i:s')
        );
        
        $bookingQueue->enqueue($bookingData);
        
        // Process booking (in a real system, this might be done by a separate process)
        $currentBooking = $bookingQueue->dequeue();
        
        if($currentBooking) {
            // Create the booking
            if($booking->create()) {
                // Set response code - 201 created
                http_response_code(201);
                
                // Tell the user
                echo json_encode(array(
                    "message" => "Booking was created.",
                    "booking_id" => $db->lastInsertId(),
                    "total_price" => $total_price
                ));
            } else {
                // Set response code - 503 service unavailable
                http_response_code(503);
                
                // Tell the user
                echo json_encode(array("message" => "Unable to create booking."));
            }
        } else {
            // Set response code - 500 internal server error
            http_response_code(500);
            
            // Tell the user
            echo json_encode(array("message" => "Error processing booking queue."));
        }
    } else {
        // Set response code - 404 Not found
        http_response_code(404);
        
        // Tell the user
        echo json_encode(array("message" => "Package not found."));
    }
} else {
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to create booking. Data is incomplete."));
}
?>