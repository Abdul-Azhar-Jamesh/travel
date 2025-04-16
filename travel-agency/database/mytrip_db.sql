-- Create database
CREATE DATABASE IF NOT EXISTS mytrip_db;
USE mytrip_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Packages table
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    rating DECIMAL(3, 1) DEFAULT 0,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    total_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
);

-- Search history table
CREATE TABLE IF NOT EXISTS search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    search_term VARCHAR(255) NOT NULL,
    search_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert admin user (password: admin123)
INSERT INTO users (firstName, lastName, email, password, role)
VALUES ('Admin', 'User', 'admin@mytrip.com', '$2y$10$8KzS.AzQDSXvj5BQtYl5.uRqQF0z9vNc4jW1Fq7pWGq0XmRJ5Lhji', 'admin');

-- Insert sample packages
INSERT INTO packages (title, description, location, price, rating, category, image) VALUES
('Bali Paradise', '7 days in tropical paradise', 'Bali, Indonesia', 1299.00, 4.8, 'Beach', 'assets/images/packages/bali.jpg'),
('European Adventure', '10 days across 4 countries', 'Multiple Cities, Europe', 2499.00, 4.9, 'Adventure', 'assets/images/packages/europe.jpg'),
('Tokyo Explorer', '5 days in Japan\'s capital', 'Tokyo, Japan', 1899.00, 4.7, 'City', 'assets/images/packages/tokyo.jpg'),
('African Safari', '8 days wildlife adventure', 'Kenya & Tanzania', 3299.00, 4.9, 'Wildlife', 'assets/images/packages/safari.jpg'),
('Caribbean Cruise', '6 days island hopping', 'Caribbean Sea', 1599.00, 4.6, 'Cruise', 'assets/images/packages/caribbean.jpg'),
('Himalayan Trek', '12 days mountain expedition', 'Nepal', 2199.00, 4.8, 'Adventure', 'assets/images/packages/himalaya.jpg'),
('Paris Romance', '5 days in the city of love', 'Paris, France', 1799.00, 4.7, 'City', 'assets/images/packages/paris.jpg'),
('Australian Outback', '10 days exploring Australia', 'Australia', 2899.00, 4.8, 'Adventure', 'assets/images/packages/australia.jpg'),
('Maldives Luxury', '7 days in paradise', 'Maldives', 3499.00, 4.9, 'Beach', 'assets/images/packages/maldives.jpg');