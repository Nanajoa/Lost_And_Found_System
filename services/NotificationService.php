<?php

class NotificationService {
    private $conn;
    private $observers = [];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addObserver($observer) {
        $this->observers[] = $observer;
    }

    public function removeObserver($observer) {
        $key = array_search($observer, $this->observers);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }

    public function notifyObservers($notification) {
        foreach ($this->observers as $observer) {
            $observer->update($notification);
        }
    }

    public function createNotification($item_id, $claimer_id, $message) {
        try {
            // Insert notification into database
            $stmt = $this->conn->prepare("INSERT INTO Notifications (item_id, claimer_id, message, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $item_id, $claimer_id, $message);
            $stmt->execute();
            $notification_id = $stmt->insert_id;
            $stmt->close();

            // Get the lost item owner's ID
            $stmt = $this->conn->prepare("SELECT user_id FROM LostItems WHERE id = ?");
            $stmt->bind_param("i", $item_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $owner_id = $result->fetch_assoc()['user_id'];
            $stmt->close();

            // Create notification data
            $notification = [
                'id' => $notification_id,
                'item_id' => $item_id,
                'claimer_id' => $claimer_id,
                'owner_id' => $owner_id,
                'message' => $message,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Notify observers
            $this->notifyObservers($notification);

            return $notification_id;
        } catch (Exception $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }

    public function getUserNotifications($user_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT n.id as notification_id, n.message, n.created_at, 
                       li.id as item_id, li.name as item_name,
                       c.id as claim_id
                FROM Notifications n
                JOIN LostItems li ON n.item_id = li.id
                JOIN Claims c ON c.item_id = li.id
                WHERE li.user_id = ? AND n.is_read = 0
                ORDER BY n.created_at DESC
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $notifications = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $notifications;
        } catch (Exception $e) {
            error_log("Error getting user notifications: " . $e->getMessage());
            return [];
        }
    }

    public function markAsRead($notification_id) {
        try {
            $stmt = $this->conn->prepare("UPDATE Notifications SET is_read = 1 WHERE id = ?");
            $stmt->bind_param("i", $notification_id);
            $stmt->execute();
            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }
} 