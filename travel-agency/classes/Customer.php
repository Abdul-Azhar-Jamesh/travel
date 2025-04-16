<?php
// Include the User class
require_once 'User.php';

// Customer class (Inheritance)
class Customer extends User {
    private $phone;
    private $address;
    
    // Constructor
    public function __construct($db) {
        parent::__construct($db);
    }
    
    // Getters and Setters
    public function getPhone() {
        return $this->phone;
    }
    
    public function setPhone($phone) {
        $this->phone = $phone;
    }
    
    public function getAddress() {
        return $this->address;
    }
    
    public function setAddress($address) {
        $this->address = $address;
    }
    
    // Implementation of abstract methods
    public function create() {
        $query = "INSERT INTO users (firstName, lastName, email, password, role, phone, address) 
                  VALUES (?, ?, ?, ?, 'customer', ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->firstName = htmlspecialchars(strip_tags($this->firstName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        
        // Bind parameters
        $stmt->bindParam(1, $this->firstName);
        $stmt->bindParam(2, $this->lastName);
        $stmt->bindParam(3, $this->email);
        $stmt->bindParam(4, $this->password);
        $stmt->bindParam(5, $this->phone);
        $stmt->bindParam(6, $this->address);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    public function read() {
        $query = "SELECT * FROM users WHERE id = ? AND role = 'customer'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            return true;
        }
        
        return false;
    }
    
    public function update() {
        $query = "UPDATE users SET 
                  firstName = ?,
                  lastName = ?,
                  email = ?,
                  phone = ?,
                  address = ?
                  WHERE id = ? AND role = 'customer'";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->firstName = htmlspecialchars(strip_tags($this->firstName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        
        // Bind parameters
        $stmt->bindParam(1, $this->firstName);
        $stmt->bindParam(2, $this->lastName);
        $stmt->bindParam(3, $this->email);
        $stmt->bindParam(4, $this->phone);
        $stmt->bindParam(5, $this->address);
        $stmt->bindParam(6, $this->id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    public function delete() {
        $query = "DELETE FROM users WHERE id = ? AND role = 'customer'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Customer-specific methods
    public function getBookings() {
        $query = "SELECT * FROM bookings WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>