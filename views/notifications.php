<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection and NotificationService
require_once __DIR__ . '/../db/database.php';
require_once __DIR__ . '/../patterns/NotificationService.php';

// Get user's ID from session
$user_id = $_SESSION['user_id'];

// Create NotificationService instance
$conn = getDatabaseConnection();
$notifications = [];

try {
    // Get notifications
    $stmt = $conn->prepare("
        SELECT n.*, c.id as claim_id, c.user_id as claimer_id, c.status as claim_status
        FROM Notifications n
        LEFT JOIN Claims c ON n.message LIKE CONCAT('%', c.lost_item_id, '%')
        WHERE n.user_id = ? AND n.user_type = ?
        ORDER BY n.date_sent DESC
    ");
    $stmt->bind_param("is", $_SESSION['user_id'], $_SESSION['user_type']);
    $stmt->execute();
    $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    echo "<p class='text-red-500'>Error loading notifications: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
  <link rel="stylesheet" as="style" onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Inter%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />

  <title>Notifications - Ayera</title>
  <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <!-- Include session management script -->
  <script src="../js/session.js"></script>
</head>

<body>
  <div class="relative flex size-full min-h-screen flex-col bg-slate-50 group/design-root overflow-x-hidden"
    style='font-family: Inter, "Noto Sans", sans-serif;'>
    <div class="layout-container flex h-full grow flex-col">

      <!-- Header -->
      <header
        class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7edf3] px-10 py-3">
        <div class="flex items-center gap-4 text-[#0e141b]">
          <a href="homepage.php" class="flex items-center gap-4">
            <div class="size-4">
              <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M13.8261 17.4264C16.7203 18.1174 20.2244 18.5217 24 18.5217C27.7756 18.5217 31.2797 18.1174 34.1739 17.4264C36.9144 16.7722 39.9967 15.2331 41.3563 14.1648L24.8486 40.6391C24.4571 41.267 23.5429 41.267 23.1514 40.6391L6.64374 14.1648C8.00331 15.2331 11.0856 16.7722 13.8261 17.4264Z"
                  fill="currentColor"></path>
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M39.998 12.236C39.9944 12.2537 39.9875 12.2845 39.9748 12.3294C39.9436 12.4399 39.8949 12.5741 39.8346 12.7175C39.8168 12.7597 39.7989 12.8007 39.7813 12.8398C38.5103 13.7113 35.9788 14.9393 33.7095 15.4811C30.9875 16.131 27.6413 16.5217 24 16.5217C20.3587 16.5217 17.0125 16.131 14.2905 15.4811C12.0212 14.9393 9.48968 13.7113 8.21867 12.8398C8.20107 12.8007 8.18319 12.7597 8.16541 12.7175C8.10506 12.5741 8.05638 12.4399 8.02517 12.3294C8.01252 12.2845 8.00563 12.2537 8.00203 12.236H39.998Z"/>
              </svg>
            </div>
            <h2 class="text-[#0e141b] text-lg font-bold leading-tight tracking-[-0.015em]">Ayera</h2>
          </a>
        </div>
        <div class="flex flex-1 justify-end gap-4">
          <a href="report-form.php">
            <button
              class="flex min-w-[84px] max-w-[200px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#308ce8] text-white text-sm font-bold leading-normal tracking-[0.015em]">
              <span class="truncate">Report Found Item</span>
            </button>
          </a>
          <a href="profile.php">
            <div class="w-10 h-10 rounded-full bg-[#e7edf3] flex items-center justify-center overflow-hidden">
              <img src="/api/placeholder/40/40" alt="Profile" class="h-full w-full object-cover" />
            </div>
          </a>
        </div>
      </header>

      <!-- Main Content -->
      <div class="flex flex-1 flex-col px-10 py-6">
        <div class="max-w-4xl mx-auto w-full">
          <h1 class="text-2xl font-bold mb-6">Notifications</h1>
          
          <?php if (empty($notifications)): ?>
            <p class="text-[#4e7397]">No notifications yet.</p>
          <?php else: ?>
            <div class="space-y-4">
              <?php foreach ($notifications as $notification): ?>
                <div class="bg-white rounded-xl p-6 shadow-sm">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="text-sm mb-2"><?php echo htmlspecialchars($notification['message']); ?></p>
                      <span class="text-xs text-[#4e7397]">
                        <?php echo date('M d, Y H:i', strtotime($notification['date_sent'])); ?>
                      </span>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Footer -->
      <footer class="bg-[#e7edf3] py-6 px-10 mt-auto text-[#2c2c2c]">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <!-- Logo + Name -->
          <div class="mb-4 md:mb-0 flex items-center gap-2">
            <div class="size-5 text-[#1a1a1a]">
              <svg viewBox="0 0 48 48" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.8261 17.4264C16.7203 18.1174 20.2244 18.5217 24 18.5217C27.7756 18.5217 31.2797 18.1174 34.1739 17.4264C36.9144 16.7722 39.9967 15.2331 41.3563 14.1648L24.8486 40.6391C24.4571 41.267 23.5429 41.267 23.1514 40.6391L6.64374 14.1648C8.00331 15.2331 11.0856 16.7722 13.8261 17.4264Z"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M39.998 12.236C39.9944 12.2537 39.9875 12.2845 39.9748 12.3294C39.9436 12.4399 39.8949 12.5741 39.8346 12.7175C39.8168 12.7597 39.7989 12.8007 39.7813 12.8398C38.5103 13.7113 35.9788 14.9393 33.7095 15.4811C30.9875 16.131 27.6413 16.5217 24 16.5217C20.3587 16.5217 17.0125 16.131 14.2905 15.4811C12.0212 14.9393 9.48968 13.7113 8.21867 12.8398C8.20107 12.8007 8.18319 12.7597 8.16541 12.7175C8.10506 12.5741 8.05638 12.4399 8.02517 12.3294C8.01252 12.2845 8.00563 12.2537 8.00203 12.236H39.998Z"/>
              </svg>
            </div>
            <span class="font-semibold text-lg">Ayera</span>
          </div>

          <!-- Navigation Links -->
          <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4 md:mb-0">
            <a href="homepage.php" class="hover:underline hover:text-black">Home</a>
          </div>
        </div>

        <div class="mt-6 text-center text-xs text-gray-500">
          Finding lost possessions made easy.
        </div>
      </footer>
    </div>
  </div>

  <script>
  function markAsRead(notificationId) {
    fetch('mark-notification-read.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ notification_id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Failed to mark notification as read');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred');
    });
  }
  </script>
</body>
</html> 