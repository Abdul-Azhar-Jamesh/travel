<?php
// Base User class (Abstraction & Encapsulation)
abstract class User {
    // Properties
    protected $id;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $password;
    protected $conn;
    
    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Getters and Setters (Encapsulation)
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getFirstName() {
        return $this->firstName;
    }
    
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }
    
    public function getLastName() {
        return $this->lastName;
    }
    
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }
    
    public function setPassword($password) {
        // Hash password for security
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }
    
    // Abstract methods that must be implemented by child classes
    abstract public function create();
    abstract public function update();
    abstract public function delete();
    abstract public function read();
    
    // Common method for all users
    public function login($email, $password) {
        $query = "SELECT id, firstName, lastName, email, password, role FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $row['id'];  // ✅ Fixing the syntax error here
            $firstName = $row['firstName'];
            $lastName = $row['lastName'];
            $email = $row['email'];
            $hashed_password = $row['password'];
            $role = $row['role'];
            
            // Verify password
            if(password_verify($password, $hashed_password)) {
                // Password is correct, create session variables
                $_SESSION['user_id'] = $id;
                $_SESSION['firstName'] = $firstName;
                $_SESSION['lastName'] = $lastName;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                
                return true;
            }
        }
        
        return false;
    }
}    
?>