<?php
require_once '../config.php';

// ================= SECURITY CHECK =================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'creator') {
    header("Location: ../index.php");
    exit();
}

$creator_id = $_SESSION['user_id'];

// ================= FETCH DYNAMIC DATA =================
$tests_count = 0;
$attendees_count = 0;
$pass_rate = 0;
$certs_count = 0;
$recent_tests = [];

// ================= TOTAL TESTS =================
$res_tests = $conn->query("
SELECT COUNT(*) as count 
FROM tests 
WHERE creator_id = '$creator_id'
");

if ($res_tests) {
    $tests_count = $res_tests->fetch_assoc()['count'];
}

// ================= TOTAL ATTENDEES =================
$res_att = $conn->query("
SELECT COUNT(DISTINCT student_id) as count
FROM test_attempts ta
JOIN tests t ON ta.test_id = t.id
WHERE t.creator_id = '$creator_id'
");

if ($res_att) {
    $attendees_count = $res_att->fetch_assoc()['count'];
}

// ================= PASS RATE =================
$res_pass = $conn->query("
SELECT (SUM(passed) / COUNT(*)) * 100 as rate
FROM test_attempts ta
JOIN tests t ON ta.test_id = t.id
WHERE t.creator_id = '$creator_id'
");

if ($res_pass && $row = $res_pass->fetch_assoc()) {
    $pass_rate = $row['rate'] ? round($row['rate']) : 0;
}

// ================= TOTAL CERTIFICATES =================
$res_certs = $conn->query("
SELECT COUNT(*) as count
FROM test_attempts ta
JOIN tests t ON ta.test_id = t.id
WHERE t.creator_id = '$creator_id'
AND ta.passed = 1
");

if ($res_certs) {
    $certs_count = $res_certs->fetch_assoc()['count'];
}

// ================= TESTS CREATED THIS WEEK =================
$tests_this_week = 0;

$res_tw = $conn->query("
SELECT COUNT(*) as count
FROM tests
WHERE creator_id = '$creator_id'
AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");

if ($res_tw) {
    $tests_this_week = $res_tw->fetch_assoc()['count'];
}

// ================= ATTENDEES THIS MONTH =================
$attendees_this_month = 0;

$res_am = $conn->query("
SELECT COUNT(DISTINCT student_id) as count
FROM test_attempts ta
JOIN tests t ON ta.test_id = t.id
WHERE t.creator_id = '$creator_id'
AND ta.completed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");

if ($res_am) {
    $attendees_this_month = $res_am->fetch_assoc()['count'];
}

// ================= RECENT PASS RATE =================
$recent_pass_rate = 0;

$res_rpr = $conn->query("
SELECT (SUM(passed) / COUNT(*)) * 100 as rate
FROM test_attempts ta
JOIN tests t ON ta.test_id = t.id
WHERE t.creator_id = '$creator_id'
AND ta.completed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");

if ($res_rpr && $row = $res_rpr->fetch_assoc()) {
    $recent_pass_rate = $row['rate'] ? round($row['rate']) : 0;
}

$pass_rate_diff = $recent_pass_rate - $pass_rate;

// ================= CERTIFICATES ISSUED TODAY =================
$certs_today = 0;

$res_ct = $conn->query("
SELECT COUNT(*) as count
FROM test_attempts ta
JOIN tests t ON ta.test_id = t.id
WHERE t.creator_id = '$creator_id'
AND ta.passed = 1
AND DATE(ta.completed_at) = CURDATE()
");

if ($res_ct) {
    $certs_today = $res_ct->fetch_assoc()['count'];
}

// ================= RECENT TESTS =================
$res_recent = $conn->query("
SELECT 
t.test_name,
t.category,
t.status,
t.created_at,
(
SELECT COUNT(*) 
FROM test_attempts 
WHERE test_id = t.id
) as attendee_count

FROM tests t
WHERE t.creator_id = '$creator_id'
ORDER BY t.created_at DESC
LIMIT 5
");

if ($res_recent) {
    while ($row = $res_recent->fetch_assoc()) {
        $recent_tests[] = $row;
    }
}

// ================= GREETING =================
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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">

    <style>
        body {
            background-color: #f8fafc;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 20px;
            padding: 35px 40px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .welcome-text h2 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .welcome-text p {
            color: #94a3b8;
            font-size: 15px;
        }

        .welcome-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 1;
        }

        .welcome-btn:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }

        .stats-grid {
            gap: 25px;
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            padding: 25px;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            color: white !important;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, #f97316, #fb923c);
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #10b981, #34d399);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .stat-icon.purple {
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .trend {
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
        }

        .trend.up {
            color: #10b981;
        }

        .trend.down {
            color: #ef4444;
        }

        .trend.neutral {
            color: #64748b;
        }

        .dash-panel {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            padding: 30px;
            background: white;
        }

        .recent-table tbody tr {
            transition: all 0.2s ease;
        }

        .recent-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .recent-table td {
            padding: 18px 10px;
            vertical-align: middle;
        }

        .action-dot {
            color: #94a3b8;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: 0.2s;
        }

        .action-dot:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .step-icon {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .btn-sm {
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        @keyframes pulse-green {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .cert-row:hover {
            background: #fbfcfe !important;
        }

        .action-btn-hybrid:hover {
            filter: brightness(0.95);
            transform: translateY(-2px);
        }

        #certSearchInput:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>

<body>
    <div class="global-toast-container" id="globalToastContainer"></div>

    <nav class="top-navbar">
        <div class="logo">
            <h1>EXAMINATION PORTAL</h1>
        </div>

        <div class="admin-box" id="adminBox">
            <span class="admin-name">
                <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>
            </span>
            <i class="fa-solid fa-user-circle profile-icon"></i>

            <div class="admin-dropdown">
                <a href="#"><i class="fa-solid fa-gear"></i> Settings</a>
                <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>
    </nav>

    <aside class="sidebar">
        <div class="nav-item active sidebar-link" data-target="dashboard-view"><i class="fa-solid fa-house"></i>
            Dashboard</div>
        <div class="nav-group" id="testsGroup">
            <div class="nav-group-title">
                <div style="display:flex; align-items:center; gap:12px;">
                    <i class="fa-regular fa-file-lines" style="color:#64748b; width:20px; text-align:center;"></i>
                    <span>Tests</span>
                </div>
                <i class="fa-solid fa-chevron-down chevron"></i>
            </div>
            <div class="sub-menu">
                <a href="#" class="sub-item sidebar-link" data-target="all-tests-view">All Tests</a>
                <a href="#" class="sub-item sidebar-link" data-target="create-test-view">Create Tests</a>
                <a href="#" class="sub-item sidebar-link" data-target="question-bank-view">Question Bank</a>
                <a href="#" class="sub-item sidebar-link" data-target="attendees-view">Attendees</a>
                <a href="#" class="sub-item sidebar-link" data-target="statistics-view">Statistics</a>
            </div>
        </div>
        <div class="nav-item sidebar-link" data-target="certificates-view"><i class="fa-solid fa-certificate"></i>
            Certificates</div>
        <div class="nav-item sidebar-link" data-target="community-view"><i class="fa-solid fa-users"></i> Community
        </div>
    </aside>

    <main class="main-content">
        <?php include 'dashboard_view.php'; ?>
        <?php include 'create_test_view.php'; ?>
        <?php include 'question_bank_view.php'; ?>

        <div id="all-tests-view" class="content-section">
            <div class="page-header">
                <h2>All Tests</h2>
            </div>
            <div class="dash-panel">
                <p>List of created tests will appear here.</p>
            </div>
        </div>
        <div id="attendees-view" class="content-section">
            <div class="page-header">
                <h2>Attendees</h2>
            </div>
            <div class="dash-panel">
                <p>View and manage your registered students.</p>
            </div>
        </div>
        <div id="statistics-view" class="content-section">
            <div class="page-header">
                <h2>Global Statistics</h2>
            </div>
            <div class="dash-panel">
                <p>Advanced charts and metrics go here.</p>
            </div>
        </div>

        <!-- /////certificate////////////// -->
        <div id="certificates-view" class="content-section">
            <?php
            // Re-calculating stats specifically for this view to ensure accuracy
            $res_total = $conn->query("SELECT COUNT(*) as count FROM test_attempts ta JOIN tests t ON ta.test_id = t.id WHERE t.creator_id = '$creator_id' AND ta.passed = 1");
            $total_certs = $res_total->fetch_assoc()['count'] ?? 0;

            $res_today = $conn->query("SELECT COUNT(*) as count FROM test_attempts ta JOIN tests t ON ta.test_id = t.id WHERE t.creator_id = '$creator_id' AND ta.passed = 1 AND DATE(ta.completed_at) = CURDATE()");
            $issued_today = $res_today->fetch_assoc()['count'] ?? 0;
            ?>

            <div class="page-header"
                style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
                <div>
                    <h2 style="font-size: 28px; font-weight: 700; color: #0f172a;">Credential Analytics</h2>
                    <p style="color: #64748b; font-size: 14px;">Monitor and manage official certifications issued to
                        your students.</p>
                </div>
                <div
                    style="background: #fff; padding: 10px 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
                    <span class="live-pulse"
                        style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block; animation: pulse-green 2s infinite;"></span>
                    <span style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase;">Live
                        System</span>
                </div>
            </div>

            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 35px;">
                <div class="stat-mini-card"
                    style="background: white; padding: 25px; border-radius: 20px; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <div
                        style="width: 55px; height: 55px; border-radius: 15px; background: #f5f3ff; color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0;">
                            <?php echo $total_certs; ?>
                        </h4>
                        <p style="font-size: 13px; color: #94a3b8; font-weight: 500; margin: 0;">Total Issued</p>
                    </div>
                </div>

                <div class="stat-mini-card"
                    style="background: white; padding: 25px; border-radius: 20px; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <div
                        style="width: 55px; height: 55px; border-radius: 15px; background: #f0fdf4; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0;">
                            <?php echo $issued_today; ?>
                        </h4>
                        <p style="font-size: 13px; color: #94a3b8; font-weight: 500; margin: 0;">Today's Velocity</p>
                    </div>
                </div>
            </div>

            <div class="dash-panel"
                style="background: white; border-radius: 24px; padding: 30px; border: 1px solid #f1f5f9; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">

                <div style="display: flex; justify-content: space-between; margin-bottom: 30px; align-items: center;">
                    <div style="position: relative; width: 100%; max-width: 350px;">
                        <i class="fa-solid fa-magnifying-glass"
                            style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" id="certSearchInput" onkeyup="filterCertificates()"
                            placeholder="Filter by name or assessment..."
                            style="width: 100%; padding: 12px 15px 12px 45px; border: 1px solid #e2e8f0; border-radius: 12px; font-family: Poppins; outline: none; transition: 0.3s; font-size: 14px;">
                    </div>
                    <button class="welcome-btn"
                        onclick="showGlobalToast('Exporting', 'Preparing your credential list...', 'success')"
                        style="background: var(--accent-purple); color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-file-export"></i> Export List
                    </button>
                </div>

                <table class="recent-table" id="certTable" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                            <th
                                style="padding: 15px; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                                Recipient</th>
                            <th
                                style="padding: 15px; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                                Assessment</th>
                            <th
                                style="padding: 15px; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                                Score</th>
                            <th
                                style="padding: 15px; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                                Issued On</th>
                            <th
                                style="padding: 15px; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; text-align: center;">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cert_list = $conn->query("
                    SELECT u.first_name, u.last_name, t.test_name, ta.score, ta.completed_at, ta.id as att_id
                    FROM test_attempts ta
                    JOIN users u ON ta.student_id = u.id
                    JOIN tests t ON ta.test_id = t.id
                    WHERE t.creator_id = '$creator_id' AND ta.passed = 1
                    ORDER BY ta.completed_at DESC
                ");

                        if ($cert_list->num_rows > 0):
                            while ($c = $cert_list->fetch_assoc()): ?>
                                <tr class="cert-row" style="border-bottom: 1px solid #f1f5f9; transition: 0.2s;">
                                    <td style="padding: 18px 15px;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div
                                                style="width: 38px; height: 38px; border-radius: 10px; background: #eef2ff; color: #6366f1; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">
                                                <?php echo strtoupper(substr($c['first_name'], 0, 1)); ?>
                                            </div>
                                            <span style="font-weight: 600; color: #1e293b;"
                                                class="student-name-cell"><?php echo htmlspecialchars($c['first_name'] . ' ' . $c['last_name']); ?></span>
                                        </div>
                                    </td>
                                    <td style="padding: 15px; color: #475569; font-weight: 500;" class="test-name-cell">
                                        <?php echo htmlspecialchars($c['test_name']); ?>
                                    </td>
                                    <td style="padding: 15px;">
                                        <span
                                            style="font-weight: 700; color: #10b981; background: #f0fdf4; padding: 4px 10px; border-radius: 8px;"><?php echo round($c['score']); ?>%</span>
                                    </td>
                                    <td style="padding: 15px; color: #94a3b8; font-size: 13px;">
                                        <?php echo date('M d, Y', strtotime($c['completed_at'])); ?>
                                    </td>
                                    <td style="padding: 15px;">
                                        <div style="display: flex; justify-content: center; gap: 10px;">
                                            <?php $base = "http://localhost/projectp"; ?>

                                            <a href="<?php echo $base; ?>/exam/certificate.php?attempt_id=<?php echo $c['att_id']; ?>"
                                                target="_blank" class="action-btn-hybrid"
                                                style="color:#3b82f6;background:#eff6ff;padding:8px 15px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
                                                <i class="fa-solid fa-eye"></i> Preview
                                            </a>

                                            <a href="<?php echo $base; ?>/exam/certificate.php?attempt_id=<?php echo $c['att_id']; ?>"
                                                target="_blank" class="action-btn-hybrid"
                                                style="color:#10b981;background:#f0fdf4;padding:8px 15px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 80px; color: #94a3b8;">
                                    <i class="fa-solid fa-award"
                                        style="font-size: 45px; color: #cbd5e1; margin-bottom: 20px;"></i>
                                    <p style="font-size: 16px;">No credentials issued yet.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            /* Live filtering logic for certificates */
            function filterCertificates() {
                let input = document.getElementById("certSearchInput");
                let filter = input.value.toLowerCase();
                let table = document.getElementById("certTable");
                let tr = table.getElementsByClassName("cert-row");

                for (let i = 0; i < tr.length; i++) {
                    let studentName = tr[i].querySelector(".student-name-cell").textContent.toLowerCase();
                    let testName = tr[i].querySelector(".test-name-cell").textContent.toLowerCase();

                    if (studentName.indexOf(filter) > -1 || testName.indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        </script>

        <div id="community-view" class="content-section">
            <div class="page-header">
                <h2>Community</h2>
            </div>
            <div class="dash-panel">
                <p>Community and forum settings go here.</p>
            </div>
        </div>
    </main>

    <script>
        /* ================= TOAST NOTIFICATION FUNCTION ================= */
        function showGlobalToast(title, message, type = 'success') {
            const container = document.getElementById('globalToastContainer');
            if (!container) return;

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

        /* ================= SIDEBAR & PROFILE TOGGLE ================= */
        document.getElementById("testsGroup").addEventListener("click", function (e) {
            if (!e.target.classList.contains("sub-item")) this.classList.toggle("active");
        });

        document.getElementById("adminBox").addEventListener("click", function (e) {
            e.stopPropagation();
            this.classList.toggle("active");
        });

        window.addEventListener("click", function () {
            const box = document.getElementById("adminBox");
            if (box) box.classList.remove("active");
        });

        /* ================= SPA VIEW SWITCHING LOGIC ================= */
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        const contentSections = document.querySelectorAll('.content-section');

        sidebarLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                sidebarLinks.forEach(item => item.classList.remove('active'));

                if (this.classList.contains('sub-item')) {
                    this.classList.add('active');
                    document.querySelector('[data-target="dashboard-view"]').classList.remove('active');
                } else {
                    this.classList.add('active');
                    document.querySelectorAll('.sub-item').forEach(i => i.classList.remove('active'));
                }

                contentSections.forEach(section => section.classList.remove('active'));
                document.getElementById(this.getAttribute('data-target')).classList.add('active');
            });
        });

        /* ================= EXAM BUILDER LOGIC ================= */
        window.qCount = 0;

        /* STEP NAVIGATION */
        function goToCreateStep(step) {
            document.querySelectorAll('.builder-step').forEach(el => {
                el.classList.remove('active');
            });

            document.getElementById('createStep' + step).classList.add('active');

            if (step === 3) {
                let titleInput = document.getElementById('ex_title');
                let title = titleInput ? titleInput.value : "";

                const displayTitle = document.getElementById('display_ex_title');
                if (displayTitle) displayTitle.innerText = title ? title : "Untitled Exam";

                if (window.qCount === 0) appendQuestionBlock();
            }
        }

        /* ADD QUESTION BLOCK */
        function appendQuestionBlock(data = null) {
            const container = document.getElementById('qBuilderContainer');
            if (!container) return;

            window.qCount++;
            const id = Date.now() + Math.floor(Math.random() * 100);

            const qText = data ? data.question : '';
            const optA = data ? data.option_a : '';
            const optB = data ? data.option_b : '';
            const optC = data ? data.option_c : '';
            const optD = data ? data.option_d : '';
            const correct = data ? data.correct_option : 'A';
            const saveBankChecked = data ? '' : 'checked';

            const block = document.createElement('div');
            block.className = 'gform-card question-block active-card';
            block.style.cssText = 'background:white; border-radius:12px; padding:30px; margin-bottom:20px; box-shadow:0 2px 10px rgba(0,0,0,0.05); position:relative; border-left:6px solid #3b82f6; transition:0.3s;';

            block.innerHTML = `
                <textarea class="q-title-input" rows="2" placeholder="Type your question here..." style="width:100%; padding:15px; border:none; background:#f8fafc; border-radius:8px; margin-bottom:15px; font-family:Poppins; outline:none; resize:none;">${qText}</textarea>

                <div class="options-wrap">
                    <div class="opt-row">
                        <input type="radio" name="correct_${id}" value="A" class="opt-radio" ${correct === 'A' ? 'checked' : ''}>
                        <input type="text" class="opt-input opt-a" placeholder="Option A" value="${optA}" style="flex:1; border:none; border-bottom:1px solid #e2e8f0; padding:8px 5px; font-size:14px; outline:none;">
                    </div>
                    <div class="opt-row">
                        <input type="radio" name="correct_${id}" value="B" class="opt-radio" ${correct === 'B' ? 'checked' : ''}>
                        <input type="text" class="opt-input opt-b" placeholder="Option B" value="${optB}" style="flex:1; border:none; border-bottom:1px solid #e2e8f0; padding:8px 5px; font-size:14px; outline:none;">
                    </div>
                    <div class="opt-row">
                        <input type="radio" name="correct_${id}" value="C" class="opt-radio" ${correct === 'C' ? 'checked' : ''}>
                        <input type="text" class="opt-input opt-c" placeholder="Option C (Optional)" value="${optC}" style="flex:1; border:none; border-bottom:1px solid #e2e8f0; padding:8px 5px; font-size:14px; outline:none;">
                    </div>
                    <div class="opt-row">
                        <input type="radio" name="correct_${id}" value="D" class="opt-radio" ${correct === 'D' ? 'checked' : ''}>
                        <input type="text" class="opt-input opt-d" placeholder="Option D (Optional)" value="${optD}" style="flex:1; border:none; border-bottom:1px solid #e2e8f0; padding:8px 5px; font-size:14px; outline:none;">
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px; padding-top:15px; border-top:1px solid #f1f5f9;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" class="save-bank-chk" ${saveBankChecked} style="accent-color:#3b82f6;">
                        <span style="font-size:13px; color:#64748b;">Save to Question Bank</span>
                    </div>

                    <div>
                        <button type="button" onclick="appendQuestionBlock()" style="background:none;border:none;color:#64748b;font-size:16px;cursor:pointer;margin-right:15px;">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                        <button type="button" onclick="this.closest('.question-block').remove()" style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            `;

            block.addEventListener('click', function () {
                document.querySelectorAll('.question-block').forEach(b => b.classList.remove('active-card'));
                this.classList.add('active-card');
            });

            container.appendChild(block);
            block.scrollIntoView({ behavior: "smooth", block: "center" });
        }

        /* ================= MODAL LOGIC FOR RANDOM QUESTIONS ================= */
        function openRandomModal() {
            const modal = document.getElementById('randomQuestionsModal');
            if (modal) {
                modal.style.display = 'flex';
                setTimeout(() => document.getElementById('randomCountInput').focus(), 100);
            }
        }

        function closeRandomModal() {
            const modal = document.getElementById('randomQuestionsModal');
            if (modal) {
                modal.style.display = 'none';
                document.getElementById('randomCountInput').value = '5';
            }
        }

        async function confirmRandomImport() {
            let countInput = document.getElementById('randomCountInput').value;
            let count = parseInt(countInput);

            if (isNaN(count) || count <= 0) {
                showGlobalToast('Invalid Number', 'Please enter a valid number of questions.', 'error');
                return;
            }

            closeRandomModal();

            try {
                let response = await fetch('random_questions.php?count=' + count);
                let questions = await response.json();

                if (questions.error) {
                    showGlobalToast('Error', questions.error, 'error');
                    return;
                }
                if (questions.length === 0) {
                    showGlobalToast('Empty Bank', "You don't have enough questions in your Question Bank yet!", 'error');
                    return;
                }

                let firstBlock = document.querySelector('.question-block');
                if (firstBlock && firstBlock.querySelector('.q-title-input').value.trim() === '') {
                    firstBlock.remove();
                }

                questions.forEach(q => {
                    appendQuestionBlock(q);
                });

                showGlobalToast('Success', `Imported ${questions.length} random questions!`, 'success');

            } catch (e) {
                console.error(e); // Logs the true error to your browser console for debugging!
                showGlobalToast('Connection Error', 'Failed to fetch questions. Ensure random_questions.php is accessible.', 'error');
            }
        }

        /* ================= PUBLISH EXAM TO DATABASE ================= */
        async function publishExam(btn) {
            const title = document.getElementById('ex_title')?.value;

            if (!title) {
                showGlobalToast('Missing Detail', 'Please enter an Exam Title in Step 2.', 'error');
                goToCreateStep(2);
                return;
            }

            const payload = {
                title: title,
                desc: document.getElementById('ex_desc')?.value || "",
                duration: document.getElementById('ex_duration')?.value || 30,
                total: document.getElementById('ex_total')?.value || 100,
                pass: document.getElementById('ex_pass')?.value || 40,
                category: document.getElementById('ex_category')?.value || "General",
                status: document.getElementById('ex_status')?.value,
                questions: []
            };

            let isValid = true;

            document.querySelectorAll('.question-block').forEach((block) => {
                const qText = block.querySelector('.q-title-input')?.value;
                const optA = block.querySelector('.opt-a')?.value;
                const optB = block.querySelector('.opt-b')?.value;

                if (!qText || !optA || !optB) {
                    isValid = false;
                    block.style.borderLeftColor = '#ef4444';
                }

                const correctRadio = block.querySelector('input[type="radio"]:checked');

                payload.questions.push({
                    text: qText,
                    a: optA,
                    b: optB,
                    c: block.querySelector('.opt-c')?.value,
                    d: block.querySelector('.opt-d')?.value,
                    correct: correctRadio ? correctRadio.value : 'A',
                    saveToBank: block.querySelector('.save-bank-chk')?.checked
                });
            });

            if (!isValid || payload.questions.length === 0) {
                showGlobalToast('Incomplete Questions', 'Please fill out the question text and at least Options A & B.', 'error');
                return;
            }

            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
            btn.style.pointerEvents = 'none';

            try {
                let res = await fetch('save_exam.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                let result = await res.text();

                if (result.trim() === 'success') {
                    showGlobalToast('Exam Published!', 'Your exam is now live and saved to the database.', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    console.error(result);
                    showGlobalToast('Save Error', 'Error saving exam. Check console.', 'error');
                    btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Publish Exam';
                    btn.style.pointerEvents = 'auto';
                }
            } catch (e) {
                console.error(e);
                showGlobalToast('Network Error', 'Could not connect to the server.', 'error');
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Publish Exam';
                btn.style.pointerEvents = 'auto';
            }
        }
    </script>
</body>

</html>