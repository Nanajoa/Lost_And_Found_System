<?php
// Observer Interface
interface Observer {
    public function update($message);
}

// Concrete Observers
class EmailObserver implements Observer {
    public function update($message) {
        echo "Email Notification: $message\n";
    }
}

class SmsObserver implements Observer {
    public function update($message) {
        echo "SMS Notification: $message\n";
    }
}

// Subject Class
class NotificationService {
    private $observers = [];

    public function addObserver(Observer $observer) {
        $this->observers[] = $observer;
    }

    public function removeObserver(Observer $observer) {
        $this->observers = array_filter($this->observers, fn($obs) => $obs !== $observer);
    }

    public function notifyObservers($message) {
        foreach ($this->observers as $observer) {
            $observer->update($message);
        }
    }
}

// Client Code
$notificationService = new NotificationService();
$emailObserver = new EmailObserver();
$smsObserver = new SmsObserver();

$notificationService->addObserver($emailObserver);
$notificationService->addObserver($smsObserver);

$notificationService->notifyObservers("New message received!");
// Output:
// Email Notification: New message received!
// SMS Notification: New message received!
?>

