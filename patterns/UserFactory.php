<?php
// Product Interface
interface User {
    public function getRole();
}

// Concrete Products
class AdminUser implements User {
    public function getRole() {
        return 'Admin';
    }
}

class StudentUser implements User {
    public function getRole() {
        return 'Student';
    }
}

// Factory Class
class UserFactory {
    public static function createUser($type) {
        switch ($type) {
            case 'admin':
                return new AdminUser();
            case 'student':
                return new StudentUser();
            default:
                throw new Exception("Invalid user type");
        }
    }
}

// Client Code
$user = UserFactory::createUser('admin');
echo $user->getRole(); // Output: Admin
?>

