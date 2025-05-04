<?php
require_once __DIR__ . '/../db/database.php';
$conn = getDatabaseConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $date_lost = $_POST['date_lost'] ?? '';
    $location_seen_at = $_POST['location_seen_at'] ?? '';
    $image_path = $_POST['image_path'] ?? '';

    try {
        $stmt = $conn->prepare("INSERT INTO LostItems (name, description, date_lost, location_seen_at, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $description, $date_lost, $location_seen_at, $image_path);
        $stmt->execute();
        echo "<p class='text-green-500'>Item reported successfully!</p>";
    } catch (Exception $e) {
        echo "<p class='text-red-500'>Error reporting item: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Found Item | Ayera</title>
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
  <link rel="stylesheet" as="style" onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Inter%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>
<body class="bg-slate-50 min-h-screen" style='font-family: Inter, "Noto Sans", sans-serif;'>
  <div class="relative flex size-full min-h-screen flex-col overflow-x-hidden">
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
        <div class="hidden md:flex space-x-6">
          <a href="homepage.php" class="text-[#4e7397] hover:text-[#308ce8]">Home</a>
          <a href="category.php" class="text-[#4e7397] hover:text-[#308ce8]">Browse Items</a>
          <a href="report-form.php" class="text-[#4e7397] hover:text-[#308ce8]">Report Item</a>
          <a href="about.php" class="text-[#4e7397] hover:text-[#308ce8]">About</a>
        </div>
        <div class="flex items-center space-x-4">
          <button class="md:hidden focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#4e7397]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <a href="report-form.php">
            <button class="flex min-w-[84px] max-w-[200px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#308ce8] text-white text-sm font-bold leading-normal tracking-[0.015em]">
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

      <main class="flex flex-1 flex-col px-10 py-6">
        <h1 class="text-2xl font-bold mb-6">Report Found Item</h1>
        <form action="report-form.php" method="POST" class="space-y-4">
          <div class="flex flex-col">
            <label for="name" class="text-sm font-medium text-[#4e7397]">Item Name</label>
            <input type="text" id="name" name="name" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
          </div>
          <div class="flex flex-col">
            <label for="description" class="text-sm font-medium text-[#4e7397]">Description</label>
            <textarea id="description" name="description" rows="4" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required></textarea>
          </div>
          <div class="flex flex-col">
            <label for="date_lost" class="text-sm font-medium text-[#4e7397]">Date Found</label>
            <input type="date" id="date_lost" name="date_lost" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
          </div>
          <div class="flex flex-col">
            <label for="location_seen_at" class="text-sm font-medium text-[#4e7397]">Location Found At</label>
            <input type="text" id="location_seen_at" name="location_seen_at" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
          </div>
          <div class="flex flex-col">
            <label for="image_path" class="text-sm font-medium text-[#4e7397]">Upload Image</label>
            <input type="file" id="image_path" name="image_path" accept="image/*" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            <small class="text-[#4e7397]">Max file size: 2MB</small>
          </div>
          <button type="submit" class="flex min-w-[84px] max-w-[200px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#308ce8] text-white text-sm font-bold leading-normal tracking-[0.015em]">
            Submit
          </button>
        </form>
      </main>

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

          <!-- Social Media Icons -->
          <div class="flex gap-4 text-gray-600">
            <a href="#" class="hover:text-black transition">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22.46 6c-.77.35-1.6.58-2.46.69a4.27 4.27 0 001.88-2.36c-.82.5-1.73.84-2.7 1.03a4.23 4.23 0 00-7.19 3.86c-3.52-.17-6.64-1.86-8.73-4.42a4.22 4.22 0 001.31 5.64A4.15 4.15 0 012.8 9v.05a4.23 4.23 0 003.39 4.14c-.73.2-1.5.23-2.25.09a4.24 4.24 0 003.95 2.93 8.48 8.48 0 01-5.25 1.81c-.34 0-.67-.02-1-.06a11.93 11.93 0 006.45 1.89c7.74 0 11.98-6.41 11.98-11.97 0-.18 0-.35-.01-.53A8.52 8.52 0 0024 4.56a8.3 8.3 0 01-2.54.7z" />
              </svg>
            </a>
            <a href="#" class="hover:text-black transition">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 5 3.66 9.13 8.44 9.88v-7H7.9v-2.88h2.54V9.41c0-2.5 1.49-3.88 3.78-3.88 1.1 0 2.25.2 2.25.2v2.47H15.4c-1.25 0-1.63.78-1.63 1.58v1.9h2.77l-.44 2.88H13.8v7C18.34 21.13 22 17 22 12z" />
              </svg>
            </a>
            <a href="#" class="hover:text-black transition">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21 6.5a2.5 2.5 0 01-2.5-2.5A2.5 2.5 0 0121 1.5a2.5 2.5 0 012.5 2.5A2.5 2.5 0 0121 6.5zm-9 1.5c-3.31 0-6 2.69-6 6v6c0 .83.67 1.5 1.5 1.5h9c.83 0 1.5-.67 1.5-1.5v-6c0-3.31-2.69-6-6-6zm0 2c2.21 0 4 1.79 4 4v6h-8v-6c0-2.21 1.79-4 4-4z" />
              </svg>
            </a>
          </div>
        </div>

        <!-- Bottom Text -->
        <div class="mt-6 text-center text-xs text-gray-500">
      Ayera
        </div>
      </footer>

    </div>
    </div>
    </body>
    </html>

<script>
  document.getElementById('image_path').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file && file.size > 2 * 1024 * 1024) { // 2MB in bytes
      alert('The file size exceeds 2MB. Please upload a smaller file.');
      event.target.value = '';
    }
  });
</script> 