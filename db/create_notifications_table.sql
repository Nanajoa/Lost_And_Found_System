CREATE TABLE IF NOT EXISTS Notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    claimer_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (item_id) REFERENCES LostItems(id) ON DELETE CASCADE,
    FOREIGN KEY (claimer_id) REFERENCES Students(id) ON DELETE CASCADE
); 