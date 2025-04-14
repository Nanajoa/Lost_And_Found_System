-- Student entity
CREATE TABLE Students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    school_id VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Staff entity
CREATE TABLE Staff (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    faculty_id VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin entity
CREATE TABLE Admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Lost item entity
CREATE TABLE LostItems (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    date_lost DATE NOT NULL,
    found_status ENUM('pending', 'resolved') NOT NULL DEFAULT 'pending',
    user_id INT NOT NULL,
    user_type ENUM('student', 'staff') NOT NULL,
    image_path VARCHAR(255), -- Store file path for the image
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL, -- Timestamp for when the item is resolved
    FOREIGN KEY (user_id) REFERENCES Students(id) ON DELETE CASCADE
    -- Foreign key for staff would need to be handled separately in app logic if polymorphic user_type is needed
);

-- Claims entity
CREATE TABLE Claims (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    user_type ENUM('student', 'staff') NOT NULL,
    lost_item_id INT NOT NULL,
    date_claimed TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    claim_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Track when the claim was made
    FOREIGN KEY (lost_item_id) REFERENCES LostItems(id) ON DELETE CASCADE
    -- Foreign key for user_id omitted due to polymorphism (handled by user_type)
);

-- Notifications entity
CREATE TABLE Notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    user_type ENUM('student', 'staff') NOT NULL,
    message TEXT NOT NULL,
    date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_status ENUM('unread', 'read') NOT NULL DEFAULT 'unread', -- Track if notification is read or not
    FOREIGN KEY (user_id) REFERENCES Students(id) ON DELETE CASCADE
    -- Foreign key for staff would need to be handled separately in app logic if polymorphic user_type is needed
);

-- Returned items entity
CREATE TABLE ReturnedItems (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lost_item_id INT NOT NULL,
    returned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lost_item_id) REFERENCES LostItems(id) ON DELETE CASCADE
);