<?php
require_once __DIR__ . '/../db/database.php';
require_once __DIR__ . '/../db/auth.php';

// Start session and check if user is logged in
session_start();
requireLogin();

// Get user information
$userType = getCurrentUserType();
$userId = getCurrentUserId();
$userName = $_SESSION['name'] ?? null;

// Get recent reports for the user
$conn = getDatabaseConnection();
$reports = [];
$error = null;

try {
    if ($userType === 'admin') {
        // Admin can see all recent reports
        $stmt = $conn->prepare("
            SELECT 
                l.id,
                l.name,
                l.description,
                l.date_lost,
                l.location_seen_at,
                l.found_status,
                l.created_at,
                l.user_type,
                CASE 
                    WHEN l.user_type = 'student' THEN s.first_name
                    WHEN l.user_type = 'staff' THEN st.first_name
                END as first_name,
                CASE 
                    WHEN l.user_type = 'student' THEN s.last_name
                    WHEN l.user_type = 'staff' THEN st.last_name
                END as last_name
            FROM LostItems l
            LEFT JOIN Students s ON l.user_type = 'student' AND l.user_id = s.id
            LEFT JOIN Staff st ON l.user_type = 'staff' AND l.user_id = st.id
            ORDER BY l.created_at DESC
            LIMIT 5
        ");
    } else {
        // Regular users can only see their own reports
        $stmt = $conn->prepare("
            SELECT 
                l.id,
                l.name,
                l.description,
                l.date_lost,
                l.location_seen_at,
                l.found_status,
                l.created_at,
                l.user_type,
                CASE 
                    WHEN l.user_type = 'student' THEN s.first_name
                    WHEN l.user_type = 'staff' THEN st.first_name
                END as first_name,
                CASE 
                    WHEN l.user_type = 'student' THEN s.last_name
                    WHEN l.user_type = 'staff' THEN st.last_name
                END as last_name
            FROM LostItems l
            LEFT JOIN Students s ON l.user_type = 'student' AND l.user_id = s.id
            LEFT JOIN Staff st ON l.user_type = 'staff' AND l.user_id = st.id
            WHERE l.user_id = ? AND l.user_type = ?
            ORDER BY l.created_at DESC
            LIMIT 5
        ");
        $stmt->bind_param("is", $userId, $userType);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $reports = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "Error loading reports: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
      rel="stylesheet"
      as="style"
      onload="this.rel='stylesheet'"
      href="https://fonts.googleapis.com/css2?display=swap&amp;family=Inter%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900"
    />

    <title>Ayera - Dashboard</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  </head>
  <body>
    <div class="relative flex size-full min-h-screen flex-col bg-slate-50 group/design-root overflow-x-hidden" style='font-family: Inter, "Noto Sans", sans-serif;'>
      <div class="layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7edf3] px-10 py-3">
          <div class="flex items-center gap-8">
            <div class="flex items-center gap-4 text-[#0e141b]">
              <a href="../index.php" class="flex items-center gap-4">
                <div class="size-4">
                  <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M13.8261 17.4264C16.7203 18.1174 20.2244 18.5217 24 18.5217C27.7756 18.5217 31.2797 18.1174 34.1739 17.4264C36.9144 16.7722 39.9967 15.2331 41.3563 14.1648L24.8486 40.6391C24.4571 41.267 23.5429 41.267 23.1514 40.6391L6.64374 14.1648C8.00331 15.2331 11.0856 16.7722 13.8261 17.4264Z"
                      fill="currentColor"
                    ></path>
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M39.998 12.236C39.9944 12.2537 39.9875 12.2845 39.9748 12.3294C39.9436 12.4399 39.8949 12.5741 39.8346 12.7175C39.8168 12.7597 39.7989 12.8007 39.7813 12.8398C38.5103 13.7113 35.9788 14.9393 33.7095 15.4811C30.9875 16.131 27.6413 16.5217 24 16.5217C20.3587 16.5217 17.0125 16.131 14.2905 15.4811C12.0012 14.9346 9.44505 13.6897 8.18538 12.8168C8.17384 12.7925 8.16216 12.767 8.15052 12.7408C8.09919 12.6249 8.05721 12.5114 8.02977 12.411C8.00356 12.3152 8.00039 12.2667 8.00004 12.2612C8.00004 12.261 8 12.2607 8.00004 12.2612C8.00004 12.2359 8.0104 11.9233 8.68485 11.3686C9.34546 10.8254 10.4222 10.2469 11.9291 9.72276C14.9242 8.68098 19.1919 8 24 8C28.8081 8 33.0758 8.68098 36.0709 9.72276C37.5778 10.2469 38.6545 10.8254 39.3151 11.3686C39.9006 11.8501 39.9857 12.1489 39.998 12.236ZM4.95178 15.2312L21.4543 41.6973C22.6288 43.5809 25.3712 43.5809 26.5457 41.6973L43.0534 15.223C43.0709 15.1948 43.0878 15.1662 43.104 15.1371L41.3563 14.1648C43.104 15.1371 43.1038 15.1374 43.104 15.1371L43.1051 15.135L43.1065 15.1325L43.1101 15.1261L43.1199 15.1082C43.1276 15.094 43.1377 15.0754 43.1497 15.0527C43.1738 15.0075 43.2062 14.9455 43.244 14.8701C43.319 14.7208 43.4196 14.511 43.5217 14.2683C43.6901 13.8679 44 13.0689 44 12.2609C44 10.5573 43.003 9.22254 41.8558 8.2791C40.6947 7.32427 39.1354 6.55361 37.385 5.94477C33.8654 4.72057 29.133 4 24 4C18.867 4 14.1346 4.72057 10.615 5.94478C8.86463 6.55361 7.30529 7.32428 6.14419 8.27911C4.99695 9.22255 3.99999 10.5573 3.99999 12.2609C3.99999 13.1275 4.29264 13.9078 4.49321 14.3607C4.60375 14.6102 4.71348 14.8196 4.79687 14.9689C4.83898 15.0444 4.87547 15.1065 4.9035 15.1529C4.91754 15.1762 4.92954 15.1957 4.93916 15.2111L4.94662 15.223L4.95178 15.2312ZM35.9868 18.996L24 38.22L12.0131 18.996C12.4661 19.1391 12.9179 19.2658 13.3617 19.3718C16.4281 20.1039 20.0901 20.5217 24 20.5217C27.9099 20.5217 31.5719 20.1039 34.6383 19.3718C35.082 19.2658 35.5339 19.1391 35.9868 18.996Z"
                      fill="currentColor"
                    ></path>
                  </svg>
                </div>
                <h2 class="text-[#0e141b] text-lg font-bold leading-tight tracking-[-0.015em]">Ayera</h2>
              </a>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-[#0e141b]">Welcome, <?php echo htmlspecialchars($userName); ?></span>
              <span class="text-[#4e7397]">(<?php echo ucfirst($userType); ?>)</span>
            </div>
          </div>
          <div class="flex flex-1 justify-end gap-4">
            <div class="flex gap-2">
              <a href="report.php">
                <button
                  class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#308ce8] text-slate-50 text-sm font-bold leading-normal tracking-[0.015em]"
                >
                  <span class="truncate">Report</span>
                </button>
              </a>
              <?php if ($userType === 'admin'): ?>
                <a href="admin/dashboard.php">
                  <button
                    class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#308ce8] text-slate-50 text-sm font-bold leading-normal tracking-[0.015em]"
                  >
                    <span class="truncate">Admin Panel</span>
                  </button>
                </a>
              <?php endif; ?>
              <a href="../actions/logout.php">
                <button
                  class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#e7edf3] text-[#0e141b] text-sm font-bold leading-normal tracking-[0.015em]"
                >
                  <span class="truncate">Log out</span>
                </button>
              </a>
            </div>
          </div>
        </header>
        <div class="px-40 flex flex-1 justify-center py-5">
          <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
            <div class="flex flex-col gap-6">
              <div class="flex flex-col gap-4">
                <h1 class="text-[#0e141b] text-2xl font-bold leading-tight">
                  Your Dashboard
                </h1>
                <?php if ($error): ?>
                  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                  </div>
                <?php endif; ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Recent Reports -->
                  <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h2 class="text-[#0e141b] text-lg font-bold mb-4">Recent Reports</h2>
                    <div class="space-y-4">
                      <?php if (empty($reports)): ?>
                        <p class="text-[#4e7397] text-sm">No reports found.</p>
                      <?php else: ?>
                        <?php foreach ($reports as $report): ?>
                          <div class="border-b border-[#e7edf3] pb-4">
                            <h3 class="text-[#0e141b] font-medium"><?php echo htmlspecialchars($report['name']); ?></h3>
                            <p class="text-[#4e7397] text-sm">
                              Reported by: <?php echo htmlspecialchars($report['first_name'] . ' ' . $report['last_name']); ?>
                            </p>
                            <p class="text-[#4e7397] text-sm">Reported on <?php echo date('M d, Y', strtotime($report['created_at'])); ?></p>
                            <p class="text-[#0e141b] text-sm mt-2"><?php echo htmlspecialchars($report['description']); ?></p>
                            <div class="flex items-center mt-2">
                              <span class="text-sm <?php echo $report['found_status'] === 'resolved' ? 'text-green-600' : 'text-yellow-600'; ?>">
                                <?php echo ucfirst($report['found_status']); ?>
                              </span>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                  <!-- Quick Actions -->
                  <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h2 class="text-[#0e141b] text-lg font-bold mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                      <a href="report.php" class="block">
                        <button class="w-full flex items-center justify-center rounded-xl h-10 px-4 bg-[#308ce8] text-slate-50 text-sm font-bold">
                          Report a Lost Item
                        </button>
                      </a>
                      <a href="../index.php" class="block">
                        <button class="w-full flex items-center justify-center rounded-xl h-10 px-4 bg-[#e7edf3] text-[#0e141b] text-sm font-bold">
                          Search Found Items
                        </button>
                      </a>
                      <?php if ($userType === 'admin'): ?>
                        <a href="admin/dashboard.php" class="block">
                          <button class="w-full flex items-center justify-center rounded-xl h-10 px-4 bg-[#308ce8] text-slate-50 text-sm font-bold">
                            Manage Reports
                          </button>
                        </a>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html> 