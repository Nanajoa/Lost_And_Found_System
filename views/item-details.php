<?php
require_once __DIR__ . '/../db/database.php';

// Get the item ID from the URL
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the item details from the database
$conn = getDatabaseConnection();
$item = null;

try {
    $stmt = $conn->prepare("
        SELECT id, name, description, date_lost, location_seen_at, image, user_id, user_type, found_status
        FROM LostItems 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
} catch (Exception $e) {
    echo "<p class='text-red-500'>Error loading item details: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// If item not found, show error message
if (!$item) {
    echo "<p class='text-red-500'>Item not found.</p>";
    exit;
}

// Handle claim submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim'])) {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Update item status
        $stmt = $conn->prepare("
            UPDATE LostItems 
            SET found_status = 'claimed' 
            WHERE id = ? AND found_status = 'pending'
        ");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("Item is already claimed or resolved");
        }

        // Commit transaction
        $conn->commit();

        // Create a notification for the item claim
        require_once __DIR__ . '/../services/NotificationService.php';
        $notificationService = new NotificationService($conn);
        $message = "Item #$item_id has been claimed.";
        $notificationService->createNotification($item_id, $_SESSION['user_id'], $message);

        // Redirect to prevent form resubmission
        header("Location: item-details.php?id=" . $item_id);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo "<p class='text-red-500'>Error processing claim: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link rel="stylesheet" as="style" onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?display=swap&amp;family=Inter%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />

    <title>Item Details - Ayera</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Include session management script -->
    <script src="../js/session.js"></script>
</head>

<body>
    <div class="relative flex size-full min-h-screen flex-col bg-slate-50 group/design-root overflow-x-hidden"
        style='font-family: Inter, "Noto Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col">
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

            <div class="flex flex-1 flex-col px-10 py-6">
                <!-- Item Details Section -->
                <div class="max-w-4xl mx-auto w-full">
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm">
                        <!-- Item Image -->
                        <div class="h-96 bg-[#e7edf3] relative">
                            <?php if ($item['image']): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="w-full h-full object-cover" />
                            <?php else: ?>
                                <img src="/api/placeholder/800/384" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="w-full h-full object-cover" />
                            <?php endif; ?>
                            
                            <?php if ($item['found_status'] === 'claimed'): ?>
                                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                                    <span class="text-white text-2xl font-bold">Claimed</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Item Information -->
                        <div class="p-6">
                            <h1 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($item['name']); ?></h1>
                            <p class="text-[#4e7397] text-sm mb-4">Found at: <?php echo htmlspecialchars($item['location_seen_at']); ?></p>
                            <p class="text-sm mb-4"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-[#4e7397]">Reported on <?php echo date('M d, Y', strtotime($item['date_lost'])); ?></span>
                                <div class="flex gap-4">
                                    <?php if ($item['found_status'] === 'pending'): ?>
                                        <form method="POST" class="inline">
                                            <button type="submit" name="claim" class="text-[#308ce8] text-sm font-medium hover:underline">Claim</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($item['found_status'] === 'claimed'): ?>
                                        <form method="POST" action="unclaim-item.php" class="inline">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="text-red-600 text-sm font-medium hover:underline">Unclaim</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="homepage.php" class="text-[#308ce8] text-sm font-medium hover:underline">Back to Home</a>
                                </div>
                            </div>
                        </div>
                    </div>
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
</body>
</html>

