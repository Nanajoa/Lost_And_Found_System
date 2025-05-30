-- Students Table
CREATE TABLE Students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    school_id VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    bio TEXT DEFAULT 'Student at Ashesi University',
    profile_picture LONGBLOB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Table
CREATE TABLE Admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- LostItems Table
CREATE TABLE LostItems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    date_lost DATE NOT NULL,
    location_seen_at VARCHAR(255),
    found_status ENUM('pending', 'claimed', 'resolved') DEFAULT 'pending',
    user_id INT NOT NULL,
    user_type ENUM('student', 'staff') NOT NULL,
    image LONGBLOB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id, user_type)
);

-- Claims Table
CREATE TABLE Claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('student') NOT NULL,
    lost_item_id INT NOT NULL,
    date_claimed TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (lost_item_id) REFERENCES LostItems(id) ON DELETE CASCADE,
    INDEX (user_id, user_type)
);

-- Notifications Table
CREATE TABLE Notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('student', 'staff') NOT NULL,
    message TEXT NOT NULL,
    date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id, user_type)
);

-- ReturnedItems Table
CREATE TABLE ReturnedItems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lost_item_id INT NOT NULL,
    returned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lost_item_id) REFERENCES LostItems(id) ON DELETE CASCADE
);