<?php
// Booking class
class Booking {
    // Properties
    private $id;
    private $user_id;
    private $package_id;
    private $booking_date;
    private $start_date;
    private $end_date;
    private $status;
    private $total_price;
    private $conn;
    
    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Getters and Setters
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }
    
    public function getPackageId() {
        return $this->package_id;
    }
    
    public function setPackageId($package_id) {
        $this->package_id = $package_id;
    }
    
    public function getBookingDate() {
        return $this->booking_date;
    }
    
    public function setBookingDate($booking_date) {
        $this->booking_date = $booking_date;
    }
    
    public function getStartDate() {
        return $this->start_date;
    }
    
    public function setStartDate($start_date) {
        $this->start_date = $start_date;
    }
    
    public function getEndDate() {
        return $this->end_date;
    }
    
    public function setEndDate($end_date) {
        $this->end_date = $end_date;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getTotalPrice() {
        return $this->total_price;
    }
    
    public function setTotalPrice($total_price) {
        $this->total_price = $total_price;
    }
    
    // CRUD Operations
    public function create() {
        $query = "INSERT INTO bookings (user_id, package_id, booking_date, start_date, end_date, status, total_price) 
                  VALUES (?, ?, NOW(), ?, ?, 'Pending', ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->package_id);
        $stmt->bindParam(3, $this->start_date);
        $stmt->bindParam(4, $this->end_date);
        $stmt->bindParam(5, $this->total_price);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    public function read() {
        $query = "SELECT * FROM bookings WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->user_id = $row['user_id'];
            $this->package_id = $row['package_id'];
            $this->booking_date = $row['booking_date'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->status = $row['status'];
            $this->total_price = $row['total_price'];
            return true;
        }
        
        return false;
    }
    
    public function update() {
        $query = "UPDATE bookings SET 
                  start_date = ?,
                  end_date = ?,
                  status = ?,
                  total_price = ?
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(1, $this->start_date);
        $stmt->bindParam(2, $this->end_date);
        $stmt->bindParam(3, $this->status);
        $stmt->bindParam(4, $this->total_price);
        $stmt->bindParam(5, $this->id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    public function delete() {
        $query = "DELETE FROM bookings WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get bookings by user
    public function getBookingsByUser($user_id) {
        $query = "SELECT b.*, p.title, p.location, p.image 
                  FROM bookings b
                  JOIN packages p ON b.package_id = p.id
                  WHERE b.user_id = ?
                  ORDER BY b.booking_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get booking details with package info
    public function getBookingDetails() {
        $query = "SELECT b.*, p.title, p.description, p.location, p.image, p.price, p.category
                  FROM bookings b
                  JOIN packages p ON b.package_id = p.id
                  WHERE b.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>