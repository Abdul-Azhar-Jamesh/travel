<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and required files
include_once '../config/database.php';
include_once '../classes/Customer.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Prepare customer object
$customer = new Customer($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if(
    !empty($data->firstName) &&
    !empty($data->lastName) &&
    !empty($data->email) &&
    !empty($data->password)
) {
    // Set customer property values
    $customer->setFirstName($data->firstName);
    $customer->setLastName($data->lastName);
    $customer->setEmail($data->email);
    $customer->setPassword($data->password);
    
    // Set optional properties if available
    if(!empty($data->phone)) {
        $customer->setPhone($data->phone);
    }
    
    if(!empty($data->address)) {
        $customer->setAddress($data->address);
    }
    
    // Create the customer
    if($customer->create()) {
        // Set response code - 201 created
        http_response_code(201);
        
        // Tell the user
        echo json_encode(array("message" => "Customer was created."));
    } else {
        // Set response code - 503 service unavailable
        http_response_code(503);
        
        // Tell the user
        echo json_encode(array("message" => "Unable to create customer."));
    }
} else {
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to create customer. Data is incomplete."));
}
?>