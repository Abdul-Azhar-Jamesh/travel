<?php
// Package class (Base class for travel packages)
class Package {
    // Properties
    protected $id;
    protected $title;
    protected $description;
    protected $location;
    protected $price;
    protected $rating;
    protected $category;
    protected $image;
    protected $conn;
    
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
    
    public function getTitle() {
        return $this->title;
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }
    
    public function getLocation() {
        return $this->location;
    }
    
    public function setLocation($location) {
        $this->location = $location;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function setPrice($price) {
        $this->price = $price;
    }
    
    public function getRating() {
        return $this->rating;
    }
    
    public function setRating($rating) {
        $this->rating = $rating;
    }
    
    public function getCategory() {
        return $this->category;
    }
    
    public function setCategory($category) {
        $this->category = $category;
    }
    
    public function getImage() {
        return $this->image;
    }
    
    public function setImage($image) {
        $this->image = $image;
    }
    
    // CRUD Operations
    public function create() {
        $query = "INSERT INTO packages (title, description, location, price, rating, category, image) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->image = htmlspecialchars(strip_tags($this->image));
        
        // Bind parameters
        $stmt->bindParam(1, $this->title);
        $stmt->bindParam(2, $this->description);
        $stmt->bindParam(3, $this->location);
        $stmt->bindParam(4, $this->price);
        $stmt->bindParam(5, $this->rating);
        $stmt->bindParam(6, $this->category);
        $stmt->bindParam(7, $this->image);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    public function read() {
        $query = "SELECT * FROM packages WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->location = $row['location'];
            $this->price = $row['price'];
            $this->rating = $row['rating'];
            $this->category = $row['category'];
            $this->image = $row['image'];
            return true;
        }
        
        return false;
    }
    
    public function update() {
        $query = "UPDATE packages SET 
                  title = ?,
                  description = ?,
                  location = ?,
                  price = ?,
                  rating = ?,
                  category = ?,
                  image = ?
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->image = htmlspecialchars(strip_tags($this->image));
        
        // Bind parameters
        $stmt->bindParam(1, $this->title);
        $stmt->bindParam(2, $this->description);
        $stmt->bindParam(3, $this->location);
        $stmt->bindParam(4, $this->price);
        $stmt->bindParam(5, $this->rating);
        $stmt->bindParam(6, $this->category);
        $stmt->bindParam(7, $this->image);
        $stmt->bindParam(8, $this->id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    public function delete() {
        $query = "DELETE FROM packages WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get all packages
    public function getAllPackages() {
        $query = "SELECT * FROM packages ORDER BY rating DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Search packages by keyword
    public function searchPackages($keyword) {
        $query = "SELECT * FROM packages 
                  WHERE title LIKE ? OR description LIKE ? OR location LIKE ? OR category LIKE ?";
        
        $keyword = "%{$keyword}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $keyword);
        $stmt->bindParam(2, $keyword);
        $stmt->bindParam(3, $keyword);
        $stmt->bindParam(4, $keyword);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Filter packages by category
    public function filterByCategory($category) {
        $query = "SELECT * FROM packages WHERE category = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Filter packages by price range
    public function filterByPriceRange($min, $max) {
        $query = "SELECT * FROM packages WHERE price BETWEEN ? AND ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $min);
        $stmt->bindParam(2, $max);
        $stmt->execute();
        
        return $stmt;
    }
}
?>