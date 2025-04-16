-- Create feedback_responses table if it doesn't exist
CREATE TABLE IF NOT EXISTS feedback_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feedback_id INT NOT NULL,
    admin_id INT NOT NULL,
    response TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feedback_id) REFERENCES feedback(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add status column to feedback table if it doesn't exist
ALTER TABLE feedback 
ADD COLUMN status ENUM('New', 'Pending', 'In Progress', 'Resolved') DEFAULT 'New';


