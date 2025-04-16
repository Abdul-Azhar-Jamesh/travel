<?php
class Feedback {
    // Database connection and table name
    private $conn;
    private $table_name = "feedback";
    
    // Object properties
    public $id;
    public $name;
    public $email;
    public $subject;
    public $message;
    public $created_at;
    
    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create feedback
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, email=:email, subject=:subject, message=:message, created_at=:created_at";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->subject = htmlspecialchars(strip_tags($this->subject));
        $this->message = htmlspecialchars(strip_tags($this->message));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":subject", $this->subject);
        $stmt->bindParam(":message", $this->message);
        $stmt->bindParam(":created_at", $this->created_at);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Read all feedback
    public function read() {
        // Query to select all records
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read single feedback
    public function readOne() {
        // Query to read single record
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Bind ID
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        $stmt->execute();
        
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set values to object properties
        if($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->subject = $row['subject'];
            $this->message = $row['message'];
            $this->created_at = $row['created_at'];
            return true;
        }
        
        return false;
    }
    
    // Delete feedback
    public function delete() {
        // Query to delete record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind id
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>