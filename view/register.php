<?php
// Start session
session_start();

// Check if already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: ../index.php");
    exit;
}

// Include auth file
require_once __DIR__ . '/../db/database.php';
require_once __DIR__ . '/../db/auth.php';

// Initialize variables
$first_name = $last_name = $email = $school_id = $password = $confirm_password = "";
$first_name_err = $last_name_err = $email_err = $school_id_err = $password_err = $confirm_password_err = $register_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate first name
    if (empty(trim($_POST["first_name"]))) {
        $first_name_err = "Please enter your first name.";
    } else {
        $first_name = trim($_POST["first_name"]);
    }
    
    // Validate last name
    if (empty(trim($_POST["last_name"]))) {
        $last_name_err = "Please enter your last name.";
    } else {
        $last_name = trim($_POST["last_name"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
        // Check if email exists
        if (emailExists($email)) {
            $email_err = "This email is already taken.";
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
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if (empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($school_id_err) && empty($password_err) && empty($confirm_password_err)) {
        $result = registerStudent($first_name, $last_name, $email, $school_id, $password);
        
        if ($result['success']) {
            // Registration successful, log in the user and redirect to dashboard
            $loginResult = authenticateUser($email, $password);
            if ($loginResult['success']) {
                startUserSession($loginResult);
                header("Location: dashboard.php");
                exit;
            }
        } else {
            $email_err = $result['message'];
        }
    }
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

    <title>Ayera - Register</title> 
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  </head>
  <body>
    <div class="relative flex size-full min-h-screen flex-col bg-slate-50 group/design-root overflow-x-hidden" style='font-family: Inter, "Noto Sans", sans-serif;'>
      <div class="layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7edf3] px-10 py-3">
          <div class="flex items-center gap-4 text-[#0e141b]">
            <!-- Made Ayera logo clickable to return to index.php -->
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
          <div class="flex flex-1 justify-end gap-8">
            <!-- Changed to link to login.php -->
            <a href="login.php">
              <button
                class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#e7edf3] text-[#0e141b] text-sm font-bold leading-normal tracking-[0.015em]"
              >
                <span class="truncate">Sign in</span>
              </button>
            </a>
          </div>
        </header>
        <div class="flex flex-1 justify-center py-5">
          <!-- Centered the content -->
          <div class="layout-content-container flex flex-col items-center w-[512px] max-w-[512px] py-5 flex-1">
            <h2 class="text-[#0e141b] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">Finders Keepers</h2>
            <p class="text-[#0e141b] text-base font-normal leading-normal pb-3 pt-1 px-4 text-center">
              Create an account to report lost items or to search for items you've found.
            </p>
            <?php 
            if(!empty($register_err)){
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded w-full max-w-[480px] mx-auto mb-4" role="alert">' . $register_err . '</div>';
            }        
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="w-full">
              <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 mx-auto">
                <label class="flex flex-col min-w-40 flex-1">
                  <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">First Name</p>
                  <input
                    name="first_name"
                    placeholder="Enter your first name"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($first_name_err)) ? 'border-red-500' : ''; ?>"
                    value="<?php echo $first_name; ?>"
                  />
                  <?php if(!empty($first_name_err)): ?>
                    <span class="text-red-500 text-sm mt-1"><?php echo $first_name_err; ?></span>
                  <?php endif; ?>
                </label>
              </div>
              <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 mx-auto">
                <label class="flex flex-col min-w-40 flex-1">
                  <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">Last Name</p>
                  <input
                    name="last_name"
                    placeholder="Enter your last name"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($last_name_err)) ? 'border-red-500' : ''; ?>"
                    value="<?php echo $last_name; ?>"
                  />
                  <?php if(!empty($last_name_err)): ?>
                    <span class="text-red-500 text-sm mt-1"><?php echo $last_name_err; ?></span>
                  <?php endif; ?>
                </label>
              </div>
              <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 mx-auto">
                <label class="flex flex-col min-w-40 flex-1">
                  <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">Email address</p>
                  <input
                    name="email"
                    placeholder="you@example.com"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>"
                    value="<?php echo $email; ?>"
                  />
                  <?php if(!empty($email_err)): ?>
                    <span class="text-red-500 text-sm mt-1"><?php echo $email_err; ?></span>
                  <?php endif; ?>
                </label>
              </div>
              <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 mx-auto">
                <label class="flex flex-col min-w-40 flex-1">
                  <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">School ID</p>
                  <input
                    name="school_id"
                    placeholder="Enter your school ID"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($school_id_err)) ? 'border-red-500' : ''; ?>"
                    value="<?php echo $school_id; ?>"
                  />
                  <?php if(!empty($school_id_err)): ?>
                    <span class="text-red-500 text-sm mt-1"><?php echo $school_id_err; ?></span>
                  <?php endif; ?>
                </label>
              </div>
              <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 mx-auto">
                <label class="flex flex-col min-w-40 flex-1">
                  <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">Password</p>
                  <input
                    name="password"
                    placeholder="Create a password"
                    type="password"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>"
                  />
                  <?php if(!empty($password_err)): ?>
                    <span class="text-red-500 text-sm mt-1"><?php echo $password_err; ?></span>
                  <?php endif; ?>
                </label>
              </div>
              <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3 mx-auto">
                <label class="flex flex-col min-w-40 flex-1">
                  <p class="text-[#0e141b] text-base font-medium leading-normal pb-2">Re-enter password</p>
                  <input
                    name="confirm_password"
                    placeholder="Enter your password again"
                    type="password"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0e141b] focus:outline-0 focus:ring-0 border-none bg-[#e7edf3] focus:border-none h-14 placeholder:text-[#4e7397] p-4 text-base font-normal leading-normal <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>"
                  />
                  <?php if(!empty($confirm_password_err)): ?>
                    <span class="text-red-500 text-sm mt-1"><?php echo $confirm_password_err; ?></span>
                  <?php endif; ?>
                </label>
              </div>
              <div class="flex px-4 py-3 max-w-[480px] mx-auto">
                <button
                  type="submit"
                  class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 flex-1 bg-[#308ce8] text-slate-50 text-sm font-bold leading-normal tracking-[0.015em]"
                >
                  <span class="truncate">Create account</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>