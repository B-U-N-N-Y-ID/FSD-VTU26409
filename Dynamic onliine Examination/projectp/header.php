<?php
// Start output buffering to prevent any "headers already sent" errors
ob_start();

require_once 'config.php';

/* Start session safely */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Toast variables */
$globalToastMsg = '';
$globalToastType = '';
$redirectUrl = '';

if (isset($_GET['login_required'])) {
    $globalToastMsg = "Please login to continue.";
    $globalToastType = "error";
}

/* ================= LOGIN ================= */

/* ================= LOGIN ================= */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_submit'])) {

    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $clean_role = strtolower(trim($user['role'] ?? 'taker'));

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $clean_role;

            $globalToastMsg = "Welcome back! Redirecting...";
            $globalToastType = "success";

            /* Correct redirect path */
            /* Redirect based on role */

            if ($clean_role === 'creator') {

                $redirectUrl = "http://localhost/projectp/Admin/navbar.php";

            } else {

                $redirectUrl = "http://localhost/projectp/index.php";

            }

        } else {

            $globalToastMsg = "Incorrect Password. Please try again.";
            $globalToastType = "error";

        }

    } else {

        $globalToastMsg = "User not found. Please register first.";
        $globalToastType = "error";

    }

}

/* Current page highlight */
$currentPage = basename($_SERVER['PHP_SELF']);

/* Timezone */
date_default_timezone_set('Asia/Kolkata');

$hour = date("H");

if ($hour < 12) {
    $greet = "Good Morning";
} elseif ($hour < 18) {
    $greet = "Good Afternoon";
} else {
    $greet = "Good Evening";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quiz Engine</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

    <link rel="stylesheet" href="./css/navhead.css">

    <style>
        /* ================= Z-INDEX FIX ================= */
        .navbar {
            position: relative;
            z-index: 10000;
        }

        /* ================= LOGGED-IN PROFILE DROPDOWN ================= */
        .user-profile-box {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            position: relative;
            padding: 6px 12px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .user-profile-box:hover {
            background: rgba(15, 23, 42, 0.04);
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: #475569;
            transition: color 0.3s ease;
        }

        .user-profile-box:hover .user-name {
            color: #0f172a;
        }

        .user-dropdown {
            position: absolute;
            top: 55px;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 14px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 8px 0;
            width: 160px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10000;
        }

        .user-dropdown::before {
            content: "";
            position: absolute;
            top: -6px;
            right: 20px;
            width: 12px;
            height: 12px;
            background: rgba(255, 255, 255, 0.95);
            border-left: 1px solid rgba(255, 255, 255, 0.6);
            border-top: 1px solid rgba(255, 255, 255, 0.6);
            transform: rotate(45deg);
        }

        .user-profile-box.active .user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            text-decoration: none;
            color: #475569;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            z-index: 2;
        }

        .user-dropdown a:hover {
            background: rgba(239, 68, 68, 0.05);
            color: #ef4444;
        }

        .user-dropdown a i {
            color: #94a3b8;
            font-size: 16px;
            transition: color 0.2s ease;
        }

        .user-dropdown a:hover i {
            color: #ef4444;
        }

        /* ================= GLOBAL TOAST NOTIFICATION CSS ================= */
        .global-toast-container {
            position: fixed;
            top: 80px;
            right: 30px;
            z-index: 100000;
            display: flex;
            flex-direction: column;
            gap: 15px;
            pointer-events: none;
        }

        .global-toast {
            background: white;
            min-width: 300px;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 15px;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            pointer-events: auto;
        }

        .global-toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .global-toast.success {
            border-left: 5px solid #22c55e;
        }

        .global-toast.error {
            border-left: 5px solid #ef4444;
        }

        .global-toast i {
            font-size: 24px;
        }

        .global-toast.success i {
            color: #22c55e;
        }

        .global-toast.error i {
            color: #ef4444;
        }

        .global-toast-content {
            display: flex;
            flex-direction: column;
        }

        .global-toast-title {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .global-toast-desc {
            font-size: 13px;
            color: #64748b;
        }
    </style>

</head>

<body>

    <div class="global-toast-container" id="globalToastContainer"></div>

    <nav class="navbar">

        <div class="logo">
            <h1>EXAMINATION PORTAL</h1>
        </div>

        <div class="nav-right">

            <?php if (!isset($_SESSION['user_id'])): ?>

                <form class="nav-login" id="navLogin" method="POST">

                    <div class="nav-username">

                        <input type="text" name="username" placeholder="Username" required>

                        <a href="./registerfree.php">Register free</a>

                    </div>

                    <div class="nav-password">

                        <input type="password" name="password" placeholder="Password" required>

                        <a href="#">Forgot password?</a>

                    </div>

                    <div class="login-area">

                        <button type="submit" name="login_submit" class="login-btn">
                            Login
                        </button>

                        <div class="stay-logged">
                            <input type="checkbox">
                            <label>Stay logged in</label>
                        </div>

                    </div>

                </form>

                <i class="fa-solid fa-user-circle profile-icon" onclick="toggleNavLogin(event)"></i>

            <?php else: ?>

                <div class="user-profile-box" id="userProfileBox" onclick="toggleProfileMenu(event)">

                    <span class="user-name">
                        <?php echo $greet . ", " . htmlspecialchars($_SESSION['first_name']); ?>
                    </span>

                    <i class="fa-solid fa-user-circle profile-icon"></i>

                    <div class="user-dropdown">

                        <a href="logout.php">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Logout
                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </div>
    </nav>


    <div class="tour-navbar">

        <a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active-nav' : ''; ?>">
            Home
        </a>

        <div class="tour-dropdown">

            <a href="./makeit.php">
                Take a Exam
                <i class="fa-solid fa-chevron-down" style="font-size:12px;margin-left:4px;"></i>
            </a>

            <div class="tour-dropdown-menu">

                <a href="makeit.php#overview">
                    <i class="fa-regular fa-file"></i> Platform Overview
                </a>

                <a href="makeit.php#create">
                    <i class="fa-solid fa-sliders"></i> Create Exams
                </a>

                <a href="./exam/conduct_exam.php">
                    <i class="fa-regular fa-clipboard"></i> Conduct Exams
                </a>

                <a href="makeit.php#analyze">
                    <i class="fa-solid fa-chart-column"></i> Analyze Results
                </a>

                <a href="makeit.php#certificates">
                    <i class="fa-solid fa-certificate"></i> Certificates
                </a>

                <a href="./question_bank_testers.php">
                    <i class="fa-solid fa-database"></i> Question Bank
                </a>

            </div>

        </div>

        <a href="faq.php">FAQ</a>
        <a href="contact.php">Contact us</a>

    </div>


    <script>

        /* ================= GLOBAL TOAST FUNCTION ================= */

        function showGlobalToast(title, message, type = 'success') {

            const container = document.getElementById('globalToastContainer');

            const toast = document.createElement('div');

            toast.className = `global-toast ${type}`;

            const iconClass = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';

            toast.innerHTML = `
        <i class="fa-solid ${iconClass}"></i>
        <div class="global-toast-content">
            <span class="global-toast-title">${title}</span>
            <span class="global-toast-desc">${message}</span>
        </div>
    `;

            container.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 10);

            setTimeout(() => {

                toast.classList.remove('show');

                setTimeout(() => toast.remove(), 500);

            }, 4000);

        }


        /* ================= SHOW TOAST + SMOOTH REDIRECT ================= */

        <?php if (!empty($globalToastMsg)): ?>

            document.addEventListener("DOMContentLoaded", function () {

                let type = "<?php echo $globalToastType; ?>";

                let title = (type === 'error') ? 'Login Failed' : 'Success';

                showGlobalToast(title, "<?php echo $globalToastMsg; ?>", type);

                <?php if (!empty($redirectUrl)): ?>

                    setTimeout(function () {

                        document.body.style.transition = "opacity 0.4s ease";

                        document.body.style.opacity = "0";

                        setTimeout(function () {

                            window.location.href = "<?php echo $redirectUrl; ?>";

                        }, 400);

                    }, 1200);

                <?php endif; ?>

            });

        <?php endif; ?>


        /* ================= NAV LOGIN TOGGLE ================= */

        function toggleNavLogin(e) {

            if (e) e.stopPropagation();

            let loginForm = document.getElementById("navLogin");

            if (loginForm) loginForm.classList.toggle("active");

        }


        /* ================= PROFILE DROPDOWN ================= */

        function toggleProfileMenu(e) {

            if (e) e.stopPropagation();

            let box = document.getElementById("userProfileBox");

            if (box) box.classList.toggle("active");

        }


        /* ================= CLOSE PROFILE DROPDOWN ON OUTSIDE CLICK ================= */

        window.addEventListener("click", function (e) {

            let box = document.getElementById("userProfileBox");

            if (box && !e.target.closest(".user-profile-box")) {

                box.classList.remove("active");

            }

        });


        /* ================= LOGIN BUTTON LOADING SPINNER ================= */

        let loginForm = document.getElementById("navLogin");

        if (loginForm) {

            loginForm.addEventListener("submit", function () {

                let btn = this.querySelector(".login-btn");

                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                btn.style.opacity = "0.7";

                btn.style.pointerEvents = "none";

            });

        }


        /* ================= AUTO OPEN LOGIN IF login_required PARAM EXISTS ================= */

        document.addEventListener("DOMContentLoaded", function () {

            const params = new URLSearchParams(window.location.search);

            if (params.get("login_required") === "1") {

                let loginForm = document.getElementById("navLogin");

                if (loginForm) {

                    loginForm.classList.add("active");

                    const usernameField = loginForm.querySelector("input[name='username']");

                    if (usernameField) usernameField.focus();

                }

            }

        });

    </script>

</body>

</html>