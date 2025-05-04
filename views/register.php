<?php
// Start session and check if user is already logged in
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: ../index.php"); // Redirect to home page if user is already logged in
    exit;
}

// Include necessary files for database and authentication
require_once __DIR__ . '/../db/database.php'; // Include database connection
require_once __DIR__ . '/../db/auth.php'; // Include authentication functions

// Initialize form variables
$first_name = $last_name = $email = $school_id = $password = $confirm_password = ""; // Initialize form variables
$first_name_err = $last_name_err = $email_err = $school_id_err = $password_err = $confirm_password_err = $register_err = ""; // Initialize error variables

// Handle POST request when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") { // Check if form is submitted

    // Validate first name
    if (empty(trim($_POST["first_name"]))) { // Check if first name is empty
        $first_name_err = "Please enter your first name."; // Set error message if first name is empty
    } else {
        $first_name = trim($_POST["first_name"]); // Trim and assign first name
    }

    // Validate last name
    if (empty(trim($_POST["last_name"]))) { // Check if last name is empty
        $last_name_err = "Please enter your last name."; // Set error message if last name is empty
    } else {
        $last_name = trim($_POST["last_name"]); // Trim and assign last name
    }

    // Validate email
    if (empty(trim($_POST["email"]))) { // Check if email is empty
        $email_err = "Please enter an email."; // Set error message if email is empty
    } else {
        $email = trim($_POST["email"]); // Trim and assign email
        // Check if email already exists in the database
        if (emailExists($email)) {
            $email_err = "This email is already taken."; // Set error message if email already exists
            echo $email_err;
        }
    }

    // Validate school ID
    if (empty(trim($_POST["school_id"]))) {
        $school_id_err = "Please enter your school ID.";
    } else {
        $school_id = trim($_POST["school_id"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password !== $confirm_password) {
            $confirm_password_err = "Passwords did not match.";
        }
    }

    // If there are no validation errors, proceed with registration
    if (empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($school_id_err) && empty($password_err) && empty($confirm_password_err)) {
        // Register the user
        $result = registerStudent($first_name, $last_name, $email, $school_id, $password);

        if ($result['success']) {
            // Registration successful, log in the user and redirect
            header("Location: login.php");
            exit;
        } else {
            $register_err = $result['message'];  // Show registration error message
        }
    }
}
?>

<html>

<head>
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
  <link rel="stylesheet" as="style" onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Inter%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />

  <title>Ayera</title>
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
          <!-- Updated to correct link path -->
          <a href="../index.php" class="flex items-center gap-4">
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
        <div class="flex flex-1 justify-end gap-8">
          <!-- Updated to correct link path -->
          <a href="login.php">
            <button
              class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#e7edf3] text-[#0e141b] text-sm font-bold leading-normal tracking-[0.015em]">
              <span class="truncate">Sign in</span>
            </button>
          </a>
        </div>
      </header>
      <div class="flex flex-1 justify-center py-5">
        <!-- Centered the content -->
        <div class="layout-content-container flex flex-col items-center w-[600px] max-w-[600px] py-5 flex-1">
          <h2 class="text-[#0e141b] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">
            Join the Ayera Community</h2>
          <p class="text-[#0e141b] text-base font-normal leading-normal pb-3 pt-1 px-4 text-center">
            Create an account to report found items or to search for items you've lost.
          </p>

          <!-- Form with proper POST method and field names -->
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
          
          <?php if (!empty($register_err)): ?>
            <div class="text-red-500 text-center mb-4"><?php echo $register_err; ?></div>
          <?php endif; ?>

          <!-- Profile Picture Upload -->
          <div class="flex max-w-[480px] flex-wrap items-center justify-center gap-4 px-4 py-3 w-full">
            <div class="flex flex-col items-center">
              <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">Profile Picture</p>
              <div class="relative">
                <div class="h-24 w-24 rounded-full bg-[#e7edf3] flex items-center justify-center overflow-hidden">
                  <img id="profile-preview" src="/api/placeholder/100/100" alt="Profile preview"
                    class="h-full w-full object-cover hidden" />
                  <svg id="default-avatar" xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-[#4e7397]"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c-4.418 0-8 1.79-8 4v2h16v-2c0-2.21-3.582-4-8-4z" />
                  </svg>
                </div>
                <label for="profile-upload"
                  class="absolute bottom-0 right-0 bg-[#308ce8] rounded-full p-2 cursor-pointer">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </label>
                <input id="profile-upload" name="profile_pic" type="file" accept="image/*" class="hidden" />
              </div>
              <p class="text-xs text-[#4e7397] mt-2">Click to upload</p>
              <p class="text-xs text-[#4e7397] mt-2">Please upload a picture of your face</p>
            </div>
          </div>

          <!-- First Name field -->
          <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 w-full">
            <label class="flex flex-col min-w-40 flex-1">
              <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">First Name <span class="text-[#e94c4c]">*</span></p>
              <input name="first_name" placeholder="Your first name"
                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($first_name_err)) ? 'border-red-500' : ''; ?>"
                value="<?php echo $first_name; ?>" required />
              <?php if (!empty($first_name_err)): ?>
                <span class="text-red-500 text-xs mt-1"><?php echo $first_name_err; ?></span>
              <?php endif; ?>
            </label>
          </div>

          <!-- Last Name field -->
          <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 w-full">
            <label class="flex flex-col min-w-40 flex-1">
              <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">Last Name <span class="text-[#e94c4c]">*</span></p>
              <input name="last_name" placeholder="Your last name"
                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($last_name_err)) ? 'border-red-500' : ''; ?>"
                value="<?php echo $last_name; ?>" required />
              <?php if (!empty($last_name_err)): ?>
                <span class="text-red-500 text-xs mt-1"><?php echo $last_name_err; ?></span>
              <?php endif; ?>
            </label>
          </div>

          <!-- Ashesi Email field -->
          <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 w-full">
            <label class="flex flex-col min-w-40 flex-1">
              <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">Ashesi Email Address <span
                  class="text-[#e94c4c]">*</span></p>
              <input name="email" placeholder="you@ashesi.edu.gh" type="email" pattern="[a-zA-Z0-9._%+-]+@ashesi\.edu\.gh$"
                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>"
                value="<?php echo $email; ?>" required />
              <p class="text-xs text-[#4e7397] mt-1">Must end with @ashesi.edu.gh</p>
              <?php if (!empty($email_err)): ?>
                <span class="text-red-500 text-xs mt-1"><?php echo $email_err; ?></span>
              <?php endif; ?>
            </label>
          </div>

          <!-- School ID field -->
          <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 w-full">
            <label class="flex flex-col min-w-40 flex-1">
              <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">School ID <span class="text-[#e94c4c]">*</span></p>
              <input name="school_id" placeholder="Your school ID"
                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($school_id_err)) ? 'border-red-500' : ''; ?>"
                value="<?php echo $school_id; ?>" required />
              <?php if (!empty($school_id_err)): ?>
                <span class="text-red-500 text-xs mt-1"><?php echo $school_id_err; ?></span>
              <?php endif; ?>
            </label>
          </div>

          <!-- Phone Number with Country Code -->
          <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 w-full">
            <label class="flex flex-col min-w-40 flex-1">
              <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">Phone Number with Country Code</p>
              <div class="flex">
                <div class="flex w-24 min-w-24 overflow-hidden rounded-l-xl text-[#0e141b] bg-[#e7edf3] h-14">
                  <select name="country_code"
                    class="w-full border-none bg-[#e7edf3] focus:border-none focus:outline-none focus:ring-0 h-full px-2 text-base font-normal">
                    <option value="+93">+93 (Afghanistan)</option>
                    <option value="+355">+355 (Albania)</option>
                    <option value="+213">+213 (Algeria)</option>
                    <option value="+376">+376 (Andorra)</option>
                    <option value="+244">+244 (Angola)</option>
                    <option value="+1">+1 (Antigua and Barbuda)</option>
                    <option value="+54">+54 (Argentina)</option>
                    <option value="+374">+374 (Armenia)</option>
                    <option value="+297">+297 (Aruba)</option>
                    <option value="+61">+61 (Australia)</option>
                    <option value="+43">+43 (Austria)</option>
                    <option value="+994">+994 (Azerbaijan)</option>
                    <option value="+1">+1 (Bahamas)</option>
                    <option value="+973">+973 (Bahrain)</option>
                    <option value="+880">+880 (Bangladesh)</option>
                    <option value="+1">+1 (Barbados)</option>
                    <option value="+375">+375 (Belarus)</option>
                    <option value="+32">+32 (Belgium)</option>
                    <option value="+501">+501 (Belize)</option>
                    <option value="+229">+229 (Benin)</option>
                    <option value="+975">+975 (Bhutan)</option>
                    <option value="+591">+591 (Bolivia)</option>
                    <option value="+387">+387 (Bosnia and Herzegovina)</option>
                    <option value="+267">+267 (Botswana)</option>
                    <option value="+55">+55 (Brazil)</option>
                    <option value="+673">+673 (Brunei)</option>
                    <option value="+359">+359 (Bulgaria)</option>
                    <option value="+226">+226 (Burkina Faso)</option>
                    <option value="+257">+257 (Burundi)</option>
                    <option value="+855">+855 (Cambodia)</option>
                    <option value="+237">+237 (Cameroon)</option>
                    <option value="+1">+1 (Canada)</option>
                    <option value="+238">+238 (Cape Verde)</option>
                    <option value="+236">+236 (Central African Republic)</option>
                    <option value="+235">+235 (Chad)</option>
                    <option value="+56">+56 (Chile)</option>
                    <option value="+86">+86 (China)</option>
                    <option value="+57">+57 (Colombia)</option>
                    <option value="+269">+269 (Comoros)</option>
                    <option value="+242">+242 (Republic of the Congo)</option>
                    <option value="+243">+243 (Democratic Republic of the Congo)</option>
                    <option value="+682">+682 (Cook Islands)</option>
                    <option value="+506">+506 (Costa Rica)</option>
                    <option value="+225">+225 (Côte d'Ivoire)</option>
                    <option value="+385">+385 (Croatia)</option>
                    <option value="+599">+599 (Curaçao)</option>
                    <option value="+357">+357 (Cyprus)</option>
                    <option value="+420">+420 (Czech Republic)</option>
                    <option value="+45">+45 (Denmark)</option>
                    <option value="+253">+253 (Djibouti)</option>
                    <option value="+1">+1 (Dominica)</option>
                    <option value="+1">+1 (Dominican Republic)</option>
                    <option value="+593">+593 (Ecuador)</option>
                    <option value="+20">+20 (Egypt)</option>
                    <option value="+503">+503 (El Salvador)</option>
                    <option value="+240">+240 (Equatorial Guinea)</option>
                    <option value="+291">+291 (Eritrea)</option>
                    <option value="+372">+372 (Estonia)</option>
                    <option value="+268">+268 (Eswatini)</option>
                    <option value="+251">+251 (Ethiopia)</option>
                    <option value="+500">+500 (Falkland Islands)</option>
                    <option value="+298">+298 (Faroe Islands)</option>
                    <option value="+679">+679 (Fiji)</option>
                    <option value="+358">+358 (Finland)</option>
                    <option value="+33">+33 (France)</option>
                    <option value="+689">+689 (French Polynesia)</option>
                    <option value="+241">+241 (Gabon)</option>
                    <option value="+220">+220 (Gambia)</option>
                    <option value="+995">+995 (Georgia)</option>
                    <option value="+49">+49 (Germany)</option>
                    <option value="+233" selected>+233 (Ghana)</option>
                    <option value="+350">+350 (Gibraltar)</option>
                    <option value="+30">+30 (Greece)</option>
                    <option value="+299">+299 (Greenland)</option>
                    <option value="+1">+1 (Grenada)</option>
                    <option value="+590">+590 (Guadeloupe)</option>
                    <option value="+502">+502 (Guatemala)</option>
                    <option value="+224">+224 (Guinea)</option>
                    <option value="+245">+245 (Guinea-Bissau)</option>
                    <option value="+592">+592 (Guyana)</option>
                    <option value="+509">+509 (Haiti)</option>
                    <option value="+504">+504 (Honduras)</option>
                    <option value="+852">+852 (Hong Kong)</option>
                    <option value="+36">+36 (Hungary)</option>
                    <option value="+354">+354 (Iceland)</option>
                    <option value="+91">+91 (India)</option>
                    <option value="+62">+62 (Indonesia)</option>
                    <option value="+964">+964 (Iraq)</option>
                    <option value="+353">+353 (Ireland)</option>
                    <option value="+972">+972 (Israel)</option>
                    <option value="+39">+39 (Italy)</option>
                    <option value="+1">+1 (Jamaica)</option>
                    <option value="+81">+81 (Japan)</option>
                    <option value="+962">+962 (Jordan)</option>
                    <option value="+254">+254 (Kenya)</option>
                    <option value="+686">+686 (Kiribati)</option>
                    <option value="+965">+965 (Kuwait)</option>
                    <option value="+996">+996 (Kyrgyzstan)</option>
                    <option value="+856">+856 (Laos)</option>
                    <option value="+371">+371 (Latvia)</option>
                    <option value="+961">+961 (Lebanon)</option>
                    <option value="+266">+266 (Lesotho)</option>
                    <option value="+231">+231 (Liberia)</option>
                    <option value="+218">+218 (Libya)</option>
                    <option value="+423">+423 (Liechtenstein)</option>
                    <option value="+370">+370 (Lithuania)</option>
                    <option value="+352">+352 (Luxembourg)</option>
                    <option value="+853">+853 (Macau)</option>
                    <option value="+389">+389 (North Macedonia)</option>
                    <option value="+261">+261 (Madagascar)</option>
                    <option value="+265">+265 (Malawi)</option>
                    <option value="+60">+60 (Malaysia)</option>
                    <option value="+960">+960 (Maldives)</option>
                    <option value="+223">+223 (Mali)</option>
                    <option value="+356">+356 (Malta)</option>
                    <option value="+692">+692 (Marshall Islands)</option>
                    <option value="+222">+222 (Mauritania)</option>
                    <option value="+230">+230 (Mauritius)</option>
                    <option value="+52">+52 (Mexico)</option>
                    <option value="+691">+691 (Micronesia)</option>
                    <option value="+373">+373 (Moldova)</option>
                    <option value="+377">+377 (Monaco)</option>
                    <option value="+976">+976 (Mongolia)</option>
                    <option value="+382">+382 (Montenegro)</option>
                    <option value="+212">+212 (Morocco)</option>
                    <option value="+258">+258 (Mozambique)</option>
                    <option value="+95">+95 (Myanmar)</option>
                    <option value="+264">+264 (Namibia)</option>
                    <option value="+674">+674 (Nauru)</option>
                    <option value="+977">+977 (Nepal)</option>
                    <option value="+31">+31 (Netherlands)</option>
                    <option value="+687">+687 (New Caledonia)</option>
                    <option value="+64">+64 (New Zealand)</option>
                    <option value="+505">+505 (Nicaragua)</option>
                    <option value="+227">+227 (Niger)</option>
                    <option value="+234">+234 (Nigeria)</option>
                    <option value="+683">+683 (Niue)</option>
                    <option value="+672">+672 (Norfolk Island)</option>
                    <option value="+850">+850 (North Korea)</option>
                    <option value="+47">+47 (Norway)</option>
                    <option value="+968">+968 (Oman)</option>
                    <option value="+92">+92 (Pakistan)</option>
                    <option value="+680">+680 (Palau)</option>
                    <option value="+970">+970 (Palestine)</option>
                    <option value="+507">+507 (Panama)</option>
                    <option value="+675">+675 (Papua New Guinea)</option>
                    <option value="+595">+595 (Paraguay)</option>
                    <option value="+51">+51 (Peru)</option>
                    <option value="+63">+63 (Philippines)</option>
                    <option value="+48">+48 (Poland)</option>
                    <option value="+351">+351 (Portugal)</option>
                    <option value="+974">+974 (Qatar)</option>
                    <option value="+40">+40 (Romania)</option>
                    <option value="+7">+7 (Russia)</option>
                    <option value="+250">+250 (Rwanda)</option>
                    <option value="+1">+1 (Saint Kitts and Nevis)</option>
                    <option value="+1">+1 (Saint Lucia)</option>
                    <option value="+1">+1 (Saint Vincent and the Grenadines)</option>
                    <option value="+685">+685 (Samoa)</option>
                    <option value="+378">+378 (San Marino)</option>
                    <option value="+239">+239 (São Tomé and Príncipe)</option>
                    <option value="+966">+966 (Saudi Arabia)</option>
                    <option value="+221">+221 (Senegal)</option>
                    <option value="+381">+381 (Serbia)</option>
                    <option value="+248">+248 (Seychelles)</option>
                    <option value="+232">+232 (Sierra Leone)</option>
                    <option value="+65">+65 (Singapore)</option>
                    <option value="+421">+421 (Slovakia)</option>
                    <option value="+386">+386 (Slovenia)</option>
                    <option value="+677">+677 (Solomon Islands)</option>
                    <option value="+252">+252 (Somalia)</option>
                    <option value="+27">+27 (South Africa)</option>
                    <option value="+82">+82 (South Korea)</option>
                    <option value="+211">+211 (South Sudan)</option>
                    <option value="+34">+34 (Spain)</option>
                    <option value="+94">+94 (Sri Lanka)</option>
                    <option value="+249">+249 (Sudan)</option>
                    <option value="+597">+597 (Suriname)</option>
                    <option value="+46">+46 (Sweden)</option>
                    <option value="+41">+41 (Switzerland)</option>
                    <option value="+963">+963 (Syria)</option>
                    <option value="+886">+886 (Taiwan)</option>
                    <option value="+992">+992 (Tajikistan)</option>
                    <option value="+255">+255 (Tanzania)</option>
                    <option value="+66">+66 (Thailand)</option>
                    <option value="+670">+670 (Timor-Leste)</option>
                    <option value="+228">+228 (Togo)</option>
                    <option value="+690">+690 (Tokelau)</option>
                    <option value="+676">+676 (Tonga)</option>
                    <option value="+1">+1 (Trinidad and Tobago)</option>
                    <option value="+216">+216 (Tunisia)</option>
                    <option value="+90">+90 (Turkey)</option>
                    <option value="+993">+993 (Turkmenistan)</option>
                    <option value="+688">+688 (Tuvalu)</option>
                    <option value="+256">+256 (Uganda)</option>
                    <option value="+380">+380 (Ukraine)</option>
                    <option value="+971">+971 (United Arab Emirates)</option>
                    <option value="+44">+44 (United Kingdom)</option>
                    <option value="+1">+1 (United States)</option>
                    <option value="+598">+598 (Uruguay)</option>
                    <option value="+998">+998 (Uzbekistan)</option>
                    <option value="+678">+678 (Vanuatu)</option>
                    <option value="+58">+58 (Venezuela)</option>
                    <option value="+84">+84 (Vietnam)</option>
                    <option value="+681">+681 (Wallis and Futuna)</option>
                    <option value="+967">+967 (Yemen)</option>
                    <option value="+260">+260 (Zambia)</option>
                    <option value="+263">+263 (Zimbabwe)</option>
                  </select>
                </div>
                <input name="phone_number" placeholder="Phone number" type="tel"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-r-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal"
                  value="" />
              </div>
            </label>
          </div>

          <!-- Password field -->
          <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 w-full">
            <label class="flex flex-col min-w-40 flex-1">
              <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">Password <span class="text-[#e94c4c]">*</span></p>
              <input name="password" placeholder="Create a password" type="password"
                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>"
                value="" required />
              <?php if (!empty($password_err)): ?>
                <span class="text-red-500 text-xs mt-1"><?php echo $password_err; ?></span>
              <?php endif; ?>
            </label>
          </div>

          <!-- Re-enter password field -->
          <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 w-full">
            <label class="flex flex-col min-w-40 flex-1">
              <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">Re-enter password <span class="text-[#e94c4c]">*</span></p>
              <input name="confirm_password" placeholder="Enter your password again" type="password"
                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>"
                value="" required />
              <?php if (!empty($confirm_password_err)): ?>
                <span class="text-red-500 text-xs mt-1"><?php echo $confirm_password_err; ?></span>
              <?php endif; ?>
            </label>
          </div>

          <!-- Create account button -->
          <div class="flex px-4 py-3 max-w-[480px] w-full">
            <button type="submit"
               class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 flex-1 bg-[#308ce8] text-slate-50 text-sm font-bold leading-normal tracking-[0.015em]">
              <span>Create account</span>
            </button>
          </div>
          </form>

          <!-- Login link -->
          <div class="flex justify-center px-4 py-3 max-w-[480px] w-full">
            <p class="text-[#4e7397] text-sm">
              Already have an account? <a href="login.php" class="text-[#308ce8] font-medium">Sign in</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Simple profile picture preview functionality
    const profileUpload = document.getElementById('profile-upload');
    const profilePreview = document.getElementById('profile-preview');
    const defaultAvatar = document.getElementById('default-avatar');

    profileUpload.addEventListener('change', function () {
      if (this.files && this.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
          profilePreview.src = e.target.result;
          profilePreview.classList.remove('hidden');
          defaultAvatar.classList.add('hidden');
        }

        reader.readAsDataURL(this.files[0]);
      }
    });
  </script>
</body>

</html>