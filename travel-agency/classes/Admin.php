<?php
// Include the User class
require_once 'User.php';

// Admin class (Inheritance)
class Admin extends User {
    private $role = 'admin';
    
    // Constructor
    public function __construct($db) {
        parent::__construct($db);
    }
    
    // Implementation of abstract methods
    public function create() {
        $query = "INSERT INTO users (firstName, lastName, email, password, role) 
                  VALUES (?, ?, ?, ?, 'admin')";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->firstName = htmlspecialchars(strip_tags($this->firstName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind parameters
        $stmt->bindParam(1, $this->firstName);
        $stmt->bindParam(2, $this->lastName);
        $stmt->bindParam(3, $this->email);
        $stmt->bindParam(4, $this->password);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    public function read() {
        $query = "SELECT * FROM users WHERE id = ? AND role = 'admin'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->email = $row['email'];
            return true;
        }
        
        return false;
    }
    
    public function update() {
        $query = "UPDATE users SET 
                  firstName = ?,
                  lastName = ?,
                  email = ?
                  WHERE id = ? AND role = 'admin'";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->firstName = htmlspecialchars(strip_tags($this->firstName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind parameters
        $stmt->bindParam(1, $this->firstName);
        $stmt->bindParam(2, $this->lastName);
        $stmt->bindParam(3, $this->email);
        $stmt->bindParam(4, $this->id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    public function delete() {
        $query = "DELETE FROM users WHERE id = ? AND role = 'admin'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Admin-specific methods
    public function getAllUsers() {
        $query = "SELECT * FROM users WHERE role = 'customer'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    public function getAllBookings() {
        $query = "SELECT b.*, u.firstName, u.lastName, p.title as package_title 
                  FROM bookings b
                  JOIN users u ON b.user_id = u.id
                  JOIN packages p ON b.package_id = p.id
                  ORDER BY b.booking_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    public function updateBookingStatus($booking_id, $status) {
        $query = "UPDATE bookings SET status = ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $status);
        $stmt->bindParam(2, $booking_id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>