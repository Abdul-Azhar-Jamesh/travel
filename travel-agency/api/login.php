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
include_once '../classes/Admin.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure email and password are not empty
if(!empty($data->email) && !empty($data->password)) {
    // First, try to login as customer
    $customer = new Customer($db);
    if($customer->login($data->email, $data->password)) {
        // Set response code - 200 OK
        http_response_code(200);
        
        // Tell the user login was successful
        echo json_encode(array(
            "message" => "Login successful.",
            "user_id" => $_SESSION['user_id'],
            "firstName" => $_SESSION['firstName'],
            "lastName" => $_SESSION['lastName'],
            "email" => $_SESSION['email'],
            "role" => $_SESSION['role']
        ));
    } 
    // If customer login fails, try admin login
    else {
        $admin = new Admin($db);
        if($admin->login($data->email, $data->password)) {
            // Set response code - 200 OK
            http_response_code(200);
            
            // Tell the user login was successful
            echo json_encode(array(
                "message" => "Login successful.",
                "user_id" => $_SESSION['user_id'],
                "firstName" => $_SESSION['firstName'],
                "lastName" => $_SESSION['lastName'],
                "email" => $_SESSION['email'],
                "role" => $_SESSION['role']
            ));
        } else {
            // Set response code - 401 Unauthorized
            http_response_code(401);
            
            // Tell the user login failed
            echo json_encode(array("message" => "Login failed. Invalid email or password."));
        }
    }
} else {
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to login. Email and password are required."));
}
?>