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

// Include database connection
require_once '../db/config.php';

// Fetch current user data
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$query = "SELECT first_name, last_name, email, school_id, phone_number, bio, profile_picture FROM Students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    
    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
    }

    // Prepare the update query based on whether a new profile picture was uploaded
    if ($profile_picture !== null) {
        $update_query = "UPDATE Students SET first_name = ?, last_name = ?, phone_number = ?, profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssbi", $first_name, $last_name, $phone_number, $profile_picture, $user_id);
    } else {
        $update_query = "UPDATE Students SET first_name = ?, last_name = ?, phone_number = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssi", $first_name, $last_name, $phone_number, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        header("Location: profile.php");
        exit();
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Ayera</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link rel="stylesheet" as="style" onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?display=swap&amp;family=Inter%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Include session management script -->
    <script src="../js/session.js"></script>
</head>
<body>
    <div class="relative flex size-full min-h-screen flex-col bg-slate-50 group/design-root overflow-x-hidden"
        style='font-family: Inter, "Noto Sans", sans-serif;'>
        <div class="layout-container flex h-full grow flex-col">
            <!-- Header -->
            <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7edf3] px-10 py-3">
                <div class="flex items-center gap-4 text-[#0e141b]">
                    <a href="homepage.php" class="flex items-center gap-4">
                        <div class="size-4">
                            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M13.8261 17.4264C16.7203 18.1174 20.2244 18.5217 24 18.5217C27.7756 18.5217 31.2797 18.1174 34.1739 17.4264C36.9144 16.7722 39.9967 15.2331 41.3563 14.1648L24.8486 40.6391C24.4571 41.267 23.5429 41.267 23.1514 40.6391L6.64374 14.1648C8.00331 15.2331 11.0856 16.7722 13.8261 17.4264Z"
                                    fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M39.998 12.236C39.9944 12.2537 39.9875 12.2845 39.9748 12.3294C39.9436 12.4399 39.8949 12.5741 39.8346 12.7175C39.8168 12.7597 39.7989 12.8007 39.7813 12.8398C38.5103 13.7113 35.9788 14.9393 33.7095 15.4811C30.9875 16.131 27.6413 16.5217 24 16.5217C20.3587 16.5217 17.0125 16.131 14.2905 15.4811C12.0012 14.9346 9.44505 13.6897 8.18538 12.8168C8.17384 12.7925 8.16216 12.767 8.15052 12.7408C8.09919 12.6249 8.05721 12.5114 8.02977 12.411C8.00356 12.3152 8.00039 12.2667 8.00004 12.2612C8.00004 12.261 8 12.2607 8.00004 12.2612C8.00004 12.2359 8.0104 11.9233 8.68485 11.3686C9.34546 10.8254 10.4222 10.2469 11.9291 9.72276C14.9242 8.68098 19.1919 8 24 8C28.8081 8 33.0758 8.68098 36.0709 9.72276C37.5778 10.2469 38.6545 10.8254 39.3151 11.3686C39.9006 11.8501 39.9857 12.1489 39.998 12.236ZM4.95178 15.2312L21.4543 41.6973C22.6288 43.5809 25.3712 43.5809 26.5457 41.6973L43.0534 15.223C43.0709 15.1948 43.0878 15.1662 43.104 15.1371L41.3563 14.1648C43.104 15.1371 43.1038 15.1374 43.104 15.1371L43.1051 15.135L43.1065 15.1325L43.1101 15.1261L43.1199 15.1082C43.1276 15.094 43.1377 15.0754 43.1497 15.0527C43.1738 15.0075 43.2062 14.9455 43.244 14.8701C43.319 14.7208 43.4196 14.511 43.5217 14.2683C43.6901 13.8679 44 13.0689 44 12.2609C44 10.5573 43.003 9.22254 41.8558 8.2791C40.6947 7.32427 39.1354 6.55361 37.385 5.94477C33.8654 4.72057 29.133 4 24 4C18.867 4 14.1346 4.72057 10.615 5.94478C8.86463 6.55361 7.30529 7.32428 6.14419 8.27911C4.99695 9.22255 3.99999 10.5573 3.99999 12.2609C3.99999 13.1275 4.29264 13.9078 4.49321 14.3607C4.60375 14.6102 4.71348 14.8196 4.79687 14.9689C4.83898 15.0444 4.87547 15.1065 4.9035 15.1529C4.91754 15.1762 4.92954 15.1957 4.93916 15.2111L4.94662 15.223L4.95178 15.2312ZM35.9868 18.996L24 38.22L12.0131 18.996C12.4661 19.1391 12.9179 19.2658 13.3617 19.3718C16.4281 20.1039 20.0901 20.5217 24 20.5217C27.9099 20.5217 31.5719 20.1039 34.6383 19.3718C35.082 19.2658 35.5339 19.1391 35.9868 18.996Z"
                                    fill="currentColor"></path>
                            </svg>
                        </div>
                        <h2 class="text-[#0e141b] text-lg font-bold leading-tight tracking-[-0.015em]">Ayera</h2>
                    </a>
                </div>
                <div class="flex gap-6 items-center">
                    <a href="homepage.php" class="text-[#4e7397] hover:text-[#308ce8] text-sm font-medium">Home</a>
                    <a href="lost-items.php" class="text-[#4e7397] hover:text-[#308ce8] text-sm font-medium">Lost Items</a>
                    <a href="found-items.php" class="text-[#4e7397] hover:text-[#308ce8] text-sm font-medium">Found Items</a>
                    <a href="report-item.php" class="text-[#4e7397] hover:text-[#308ce8] text-sm font-medium">Report Item</a>
                    <div class="relative group">
                        <button class="flex items-center gap-2">
                            <img src="/api/placeholder/32/32" alt="Profile" class="w-8 h-8 rounded-full object-cover border-2 border-[#308ce8]" />
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#4e7397]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 py-2 bg-white rounded-xl shadow-lg hidden group-hover:block z-10">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-[#0e141b] hover:bg-[#e7edf3]">My Profile</a>
                            <a href="../index.php" class="block px-4 py-2 text-sm text-[#e94c4c] hover:bg-[#e7edf3]">Log Out</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex flex-1 justify-center py-8">
                <div class="layout-content-container flex flex-col w-full max-w-3xl px-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-[#0e141b] text-2xl font-bold">Edit Profile</h1>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm overflow-hidden p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-[#4e7397] mb-2">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="w-full px-3 py-2 border border-[#e7edf3] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#308ce8]" required>
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-[#4e7397] mb-2">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="w-full px-3 py-2 border border-[#e7edf3] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#308ce8]" required>
                            </div>
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-[#4e7397] mb-2">Phone Number</label>
                                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" class="w-full px-3 py-2 border border-[#e7edf3] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#308ce8]">
                            </div>
                            <div>
                                <label for="school_id" class="block text-sm font-medium text-[#4e7397] mb-2">School ID</label>
                                <input type="text" id="school_id" name="school_id" value="<?php echo htmlspecialchars($user['school_id']); ?>" class="w-full px-3 py-2 border border-[#e7edf3] rounded-lg bg-gray-100" readonly>
                            </div>
                            <div class="md:col-span-2">
                                <label for="profile_picture" class="block text-sm font-medium text-[#4e7397] mb-2">Profile Picture</label>
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="w-full px-3 py-2 border border-[#e7edf3] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#308ce8]">
                                <?php if ($user['profile_picture']): ?>
                                    <div class="mt-2">
                                        <p class="text-sm text-[#4e7397]">Current Profile Picture:</p>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_picture']); ?>" alt="Current Profile Picture" class="w-32 h-32 rounded-full object-cover mt-2">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-[#308ce8] text-white px-4 py-2 rounded-lg hover:bg-[#1a70c5]">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 