-- Create database
CREATE DATABASE IF NOT EXISTS mytrip_db;
USE mytrip_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create packages table
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    location VARCHAR(255) NOT NULL,
    duration VARCHAR(50),
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2),
    image VARCHAR(255),
    gallery TEXT,
    rating DECIMAL(3, 1) DEFAULT 0,
    featured BOOLEAN DEFAULT 0,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    travel_date DATE NOT NULL,
    adults INT NOT NULL DEFAULT 1,
    children INT DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Pending',
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
);

-- Create feedback table
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100),
    email VARCHAR(100),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    rating INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create search_history table
CREATE TABLE IF NOT EXISTS search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    search_term VARCHAR(255) NOT NULL,
    search_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create company_info table
CREATE TABLE IF NOT EXISTS company_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    mission TEXT,
    vision TEXT,
    founded VARCHAR(20),
    team_members VARCHAR(50),
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(100),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create team_members table
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    bio TEXT,
    image VARCHAR(255),
    email VARCHAR(100),
    linkedin VARCHAR(255),
    twitter VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin user
INSERT INTO users (first_name, last_name, email, password, role) 
VALUES ('Admin', 'User', 'admin@mytrip.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Password: password

-- Insert sample categories
INSERT INTO categories (name, slug, description, image) VALUES
('Beach', 'beach', 'Relax on beautiful beaches around the world', 'assets/images/beach.jpg'),
('Adventure', 'adventure', 'Thrilling experiences for the adventurous traveler', 'assets/images/adventure.jpg'),
('City', 'city', 'Explore the world\'s most exciting cities', 'assets/images/city.jpg'),
('Wildlife', 'wildlife', 'Get close to nature and amazing animals', 'assets/images/wildlife.jpg'),
('Cruise', 'cruise', 'Sail the seas in luxury and comfort', 'assets/images/cruise.jpg');

-- Insert sample packages
INSERT INTO packages (title, slug, description, location, duration, price, image, rating, featured, category_id) VALUES
('Bali Paradise', 'bali-paradise', '7 days in tropical paradise with beautiful beaches and cultural experiences', 'Bali, Indonesia', '7 days', 1299.00, 'assets/images/bali.jpg', 4.8, 1, 1),
('European Adventure', 'european-adventure', '10 days across 4 countries exploring the best of Europe', 'Multiple Cities, Europe', '10 days', 2499.00, 'assets/images/europe.jpg', 4.9, 1, 3),
('Tokyo Explorer', 'tokyo-explorer', '5 days in Japan\'s capital exploring the blend of traditional and modern', 'Tokyo, Japan', '5 days', 1899.00, 'assets/images/tokyo.jpg', 4.7, 1, 3),
('African Safari', 'african-safari', '8 days wildlife adventure in the heart of Africa', 'Kenya & Tanzania', '8 days', 3299.00, 'assets/images/safari.jpg', 4.9, 0, 4),
('Caribbean Cruise', 'caribbean-cruise', '6 days island hopping in the beautiful Caribbean', 'Caribbean Sea', '6 days', 1599.00, 'assets/images/caribbean.jpg', 4.6, 0, 5),
('Himalayan Trek', 'himalayan-trek', '12 days mountain expedition in the majestic Himalayas  4.6, 0, 5),
('Himalayan Trek', 'himalayan-trek', '12 days mountain expedition in the majestic Himalayas', 'Nepal', '12 days', 2199.00, 'assets/images/himalaya.jpg', 4.8, 0, 2),
('Paris Romance', 'paris-romance', '5 days in the city of love experiencing French culture', 'Paris, France', '5 days', 1799.00, 'assets/images/paris.jpg', 4.7, 0, 3),
('Australian Outback', 'australian-outback', '10 days exploring Australia\'s unique landscapes and wildlife', 'Australia', '10 days', 2899.00, 'assets/images/australia.jpg', 4.8, 0, 2),
('Maldives Luxury', 'maldives-luxury', '7 days in paradise with overwater bungalows and crystal clear waters', 'Maldives', '7 days', 3499.00, 'assets/images/maldives.jpg', 4.9, 0, 1);

-- Insert company info
INSERT INTO company_info (name, description, mission, vision, founded, team_members, address, phone, email) VALUES
('My Trip', 'Your trusted travel companion for discovering the world\'s most amazing destinations.', 'To provide unforgettable travel experiences that enrich lives and create lasting memories.', 'To be the leading travel agency known for exceptional service and unique travel experiences.', '2010', '25+', '123 Travel Street, New York, NY 10001, United States', '+1 (555) 123-4567', 'info@mytrip.com');

-- Insert team members
INSERT INTO team_members (name, position, bio, image, email, linkedin, twitter) VALUES
('John Doe', 'CEO & Founder', 'With over 15 years of experience in the travel industry, John leads our team with passion and vision.', 'assets/images/team-1.jpg', 'john@mytrip.com', 'https://linkedin.com/in/johndoe', 'https://twitter.com/johndoe'),
('Jane Smith', 'Travel Director', 'Jane\'s extensive knowledge of global destinations helps us create unique and authentic travel experiences.', 'assets/images/team-2.jpg', 'jane@mytrip.com', 'https://linkedin.com/in/janesmith', 'https://twitter.com/janesmith'),
('Michael Johnson', 'Customer Experience Manager', 'Michael ensures that every client receives personalized service and has an unforgettable travel experience.', 'assets/images/team-3.jpg', 'michael@mytrip.com', 'https://linkedin.com/in/michaeljohnson', 'https://twitter.com/michaeljohnson');

