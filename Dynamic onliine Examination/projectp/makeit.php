<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location:index.php?login_required=1");
    exit();
}
$user_id = $_SESSION['user_id'] ?? 0;

/* --- CORRECTED REAL-TIME STATS LOGIC --- */

// 1. Total Exams Attended (Strictly completed ones)
$total_exams_res = $conn->query("SELECT COUNT(id) as total FROM test_attempts WHERE student_id = '$user_id' AND status = 'Completed'");
$total_exams = $total_exams_res->fetch_assoc()['total'] ?? 0;

// 2. Average Score
$avg_score_res = $conn->query("SELECT AVG(score) as average FROM test_attempts WHERE student_id = '$user_id' AND status = 'Completed'");
$avg_score = round($avg_score_res->fetch_assoc()['average'] ?? 0);

// 3. Questions Answered (FIXED: Join questions table to count items per test attempt)
$q_answered_res = $conn->query("
    SELECT SUM(q_count.total_q) as total_qs
    FROM test_attempts ta
    JOIN (
        SELECT test_id, COUNT(*) as total_q
        FROM questions
        GROUP BY test_id
    ) q_count ON ta.test_id = q_count.test_id
    WHERE ta.student_id = '$user_id' AND ta.status = 'Completed'
");
$total_qs_answered = $q_answered_res->fetch_assoc()['total_qs'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Smarter Assessments - CHECKNOW</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./css/navhead.css">

    <style>
        :root {
            --accent-red: #ff3b1f;
            --accent-blue: #3b82f6;
            --bg-light: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg-light);
            min-height: 100vh;
        }

        .page-wrapper {
            padding-top: 20px;
        }

        .layout {
            display: flex;
            min-height: calc(100vh - 140px);
            gap: 20px;
            padding: 0 20px;
        }

        .sidebar {
            width: 280px;
            background: white;
            padding: 30px 15px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 8px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            text-decoration: none;
            color: #64748b;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 500;
        }

        .sidebar a:hover {
            background: #f1f5f9;
            color: var(--accent-red);
            transform: translateX(5px);
        }

        .sidebar a.active {
            background: #ffe5df;
            color: var(--accent-red);
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 59, 31, 0.1);
        }

        .content {
            flex: 1;
            background: white;
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.02);
        }

        .content-section {
            display: none;
            animation: fadeIn 0.4s ease-out;
        }

        .content-section.active {
            display: block;
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

        .exam-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .exam-card {
            background: white;
            border: 1px solid #f1f5f9;
            padding: 25px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: 0.3s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .exam-card:hover {
            border-color: var(--accent-red);
            box-shadow: 0 15px 30px rgba(255, 59, 31, 0.08);
            transform: translateY(-5px);
        }

        .category-pill {
            background: #eff6ff;
            color: var(--accent-blue);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: inline-block;
        }

        .btn-start {
            background: var(--accent-red);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-start:hover {
            background: #e0351a;
            transform: scale(1.02);
        }

        .history-btn {
            background: #0f172a;
            color: white;
            padding: 12px 22px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            text-align: left;
            padding: 15px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            border-bottom: 2px solid #f1f5f9;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-box {
            background: white;
            padding: 40px;
            border-radius: 25px;
            width: 400px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            animation: modalIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .modal-box input {
            width: 100%;
            padding: 15px;
            margin: 20px 0;
            border: 2px solid #f1f5f9;
            border-radius: 15px;
            outline: none;
            text-align: center;
            font-size: 16px;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
        }

        .btn-confirm {
            flex: 2;
            background: var(--accent-red);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-cancel {
            flex: 1;
            background: #f1f5f9;
            color: #64748b;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        /* NEW STAT CARDS STYLE */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
        }

        .stat-info p {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }

        .stat-card.premium-card {
            background: #fff;
            padding: 30px;
            border-radius: 28px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
            transition: 0.3s ease;
        }

        .bg-icon-overlay {
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 80px;
            color: #eef2ff;
            z-index: 0;
            opacity: 0.5;
        }

        .stat-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .live-pulse {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
            animation: pulse-green 2s infinite;
        }

        .performance-insight {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 40px;
            border-radius: 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .activity-box {
            background: #fff;
            padding: 35px;
            border-radius: 30px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .btn-primary-orange {
            background: var(--accent-red);
            border: none;
            color: #fff;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        @keyframes pulse-green {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }

        #analyze .stat-card:hover {
            transform: scale(1.01);
            border-color: var(--accent-blue);
        }

        /* Animation for SVG progress bar */
        @keyframes progress {
            from {
                stroke-dashoffset: 157;
            }
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="page-wrapper">
        <div class="layout">

            <aside class="sidebar">
                <ul>
                    <li><a class="menu-link active" data-section="overview"><i class="fa-regular fa-file"></i> Platform
                            Overview</a></li>
                    <li><a class="menu-link" data-section="create"><i class="fa-solid fa-sliders"></i> Create Exams</a>
                    </li>
                    <li><a class="menu-link" data-section="conduct"><i class="fa-regular fa-clipboard"></i> Conduct
                            Exams</a></li>
                    <li><a class="menu-link" data-section="questionbank"><i class="fa-solid fa-database"></i> Question
                            Bank</a></li>
                    <li><a class="menu-link" data-section="analyze"><i class="fa-solid fa-chart-column"></i> Analyze
                            Results</a></li>
                    <li><a class="menu-link" data-section="certificates"><i class="fa-solid fa-award"></i>
                            Certificates</a></li>
                    <li><a class="menu-link" data-section="monitor"><i class="fa-solid fa-desktop"></i> Monitoring
                            System</a></li>
                </ul>
            </aside>

            <main class="content">

                <section id="overview" class="content-section active">
                    <?php
                    // Precise calculations for real-time dashboard accuracy
                    $user_id = $_SESSION['user_id'];

                    // 1. Get Actual Correct and Incorrect counts across all completed exams
                    $stats_query = $conn->query("
        SELECT 
            COUNT(id) as total_completed,
            AVG(score) as avg_accuracy,
            SUM(correct_answers) as total_correct,
            SUM(wrong_answers) as total_wrong
        FROM test_attempts 
        WHERE student_id = '$user_id' AND status = 'Completed'
    ");
                    $stats = $stats_query->fetch_assoc();

                    // 2. Count Total Questions Mastered (based on actual database questions)
                    $total_exams = $stats['total_completed'] ?? 0;
                    $avg_score = round($stats['avg_accuracy'] ?? 0);
                    $total_qs_answered = $stats['total_correct'] ?? 0; // "Mastered" specifically refers to correct answers
                    ?>

                    <div
                        style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
                        <div>
                            <h1 style="font-size: 32px; color: #0f172a; margin: 0;">Tester Dashboard</h1>
                            <p style="color: #64748b; margin-top: 5px;">Welcome back to <span
                                    style="color: var(--accent-red); font-weight: 700;">CHECKNOW</span>. Monitoring live
                                performance data.</p>
                        </div>
                        <div
                            style="display: flex; align-items: center; gap: 8px; background: #fff; padding: 8px 16px; border-radius: 100px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #e2e8f0;">
                            <span class="live-pulse"></span>
                            <span
                                style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Real-Time
                                Sync</span>
                        </div>
                    </div>

                    <div class="stat-grid"
                        style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 30px;">

                        <div class="stat-card premium-card">
                            <div class="bg-icon-overlay"><i class="fa-solid fa-file-pen"></i></div>
                            <div style="position: relative; z-index: 1;">
                                <div class="stat-icon-box" style="background: #eef2ff; color: #6366f1;">
                                    <i class="fa-solid fa-file-pen"></i>
                                </div>
                                <h3><?php echo $total_exams; ?></h3>
                                <p>Exams Completed</p>
                            </div>
                        </div>

                        <div class="stat-card premium-card">
                            <div class="bg-icon-overlay" style="color: #fff7ed;"><i
                                    class="fa-solid fa-check-double"></i></div>
                            <div style="position: relative; z-index: 1;">
                                <div class="stat-icon-box" style="background: #fff7ed; color: #f59e0b;">
                                    <i class="fa-solid fa-check-double"></i>
                                </div>
                                <h3><?php echo $total_qs_answered; ?></h3>
                                <p>Questions Mastered</p>
                            </div>
                        </div>

                        <div class="stat-card premium-card">
                            <div class="bg-icon-overlay" style="color: #f0fdf4;"><i class="fa-solid fa-star"></i></div>
                            <div style="position: relative; z-index: 1;">
                                <div class="stat-icon-box" style="background: #f0fdf4; color: #22c55e;">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <h3><?php echo $avg_score; ?>%</h3>
                                <p>Average Precision</p>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 25px;">
                        <div class="performance-insight">
                            <div>
                                <h2 style="font-size: 24px; margin-bottom: 10px;">Academic Intelligence</h2>
                                <p style="color: #94a3b8; margin-bottom: 25px; max-width: 320px;">Your verified
                                    performance across <?php echo $total_exams; ?> completed modules. Accuracy is
                                    calculated based on total points earned.</p>
                                <button onclick="document.querySelector('[data-section=\'analyze\']').click()"
                                    class="btn-primary-orange">
                                    Deep Analytics Report
                                </button>
                            </div>
                            <div style="font-size: 60px; color: rgba(255,255,255,0.1);"><i
                                    class="fa-solid fa-chart-line"></i></div>
                        </div>

                        <div class="activity-box">
                            <h4
                                style="font-size: 16px; color: #0f172a; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                                <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent-blue);"></i> Live
                                Feed
                            </h4>
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <?php
                                $recent_query = $conn->query("SELECT t.test_name, ta.completed_at FROM test_attempts ta JOIN tests t ON ta.test_id = t.id WHERE ta.student_id = '$user_id' ORDER BY ta.completed_at DESC LIMIT 3");
                                while ($recent = $recent_query->fetch_assoc()):
                                    ?>
                                    <div
                                        style="display: flex; justify-content: space-between; font-size: 14px; color: #64748b;">
                                        <span>Finished <?php echo htmlspecialchars($recent['test_name']); ?></span>
                                        <span
                                            style="font-weight: 600; color: #0f172a;"><?php echo date('H:i', strtotime($recent['completed_at'])); ?></span>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- /////////////create exam//////////////// -->

<section id="create" class="content-section">

    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 35px;">
        <div>
            <h1 style="font-size: 28px; color: #0f172a; margin-bottom: 10px;">How CheckNow Works</h1>
            <p style="color: #64748b; max-width: 600px;">
                Understand how the CHECKNOW assessment system operates from high-fidelity test creation to automated certification.
            </p>
        </div>
        <a href="registerfree.php" style="background: var(--accent-red); color: white; padding: 14px 28px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 14px; box-shadow: 0 10px 20px rgba(255, 59, 31, 0.15); transition: 0.3s; display: flex; align-items: center; gap: 10px;">
            Get Started Free <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-bottom: 40px;">

        <div style="background: white; padding: 30px; border-radius: 20px; border: 1px solid #f1f5f9; text-align: center; transition: 0.3s; position: relative;">
            <div style="width: 60px; height: 60px; background: #eef2ff; color: #6366f1; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 22px; margin: auto; margin-bottom: 15px;">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <h3 style="font-size: 18px; margin-bottom: 8px;">1. Create Exam</h3>
            <p style="font-size: 13px; color: #64748b;">Administrators design exams using the smart builder with dynamic question types.</p>
        </div>

        <div style="background: white; padding: 30px; border-radius: 20px; border: 1px solid #f1f5f9; text-align: center; transition: 0.3s;">
            <div style="width: 60px; height: 60px; background: #fff7ed; color: #f59e0b; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 22px; margin: auto; margin-bottom: 15px;">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <h3 style="font-size: 18px; margin-bottom: 8px;">2. Apply Settings</h3>
            <p style="font-size: 13px; color: #64748b;">Configure proctoring rules, duration, and access passwords for security.</p>
        </div>

        <div style="background: white; padding: 30px; border-radius: 20px; border: 1px solid #f1f5f9; text-align: center; transition: 0.3s;">
            <div style="width: 60px; height: 60px; background: #ecfdf5; color: #22c55e; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 22px; margin: auto; margin-bottom: 15px;">
                <i class="fa-solid fa-link"></i>
            </div>
            <h3 style="font-size: 18px; margin-bottom: 8px;">3. Share Test Link</h3>
            <p style="font-size: 13px; color: #64748b;">Assign links to registered groups or individual test takers securely.</p>
        </div>

        <div style="background: white; padding: 30px; border-radius: 20px; border: 1px solid #f1f5f9; text-align: center; transition: 0.3s;">
            <div style="width: 60px; height: 60px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 22px; margin: auto; margin-bottom: 15px;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <h3 style="font-size: 18px; margin-bottom: 8px;">4. Instant Results</h3>
            <p style="font-size: 13px; color: #64748b;">CheckNow instantly grading and provides detailed performance analytics.</p>
        </div>

    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px;">

        <div style="background: white; padding: 25px; border-radius: 18px; border: 1px solid #f1f5f9; display: flex; gap: 20px; align-items: center;">
            <div style="font-size: 24px; color: #0ea5e9;"><i class="fa-solid fa-chart-column"></i></div>
            <div>
                <h4 style="margin-bottom: 4px;">Deep Analytics</h4>
                <p style="font-size: 12px; color: #64748b;">Reports on accuracy, time analysis, and growth insights.</p>
            </div>
        </div>

        <div style="background: white; padding: 25px; border-radius: 18px; border: 1px solid #f1f5f9; display: flex; gap: 20px; align-items: center;">
            <div style="font-size: 24px; color: #f59e0b;"><i class="fa-solid fa-award"></i></div>
            <div>
                <h4 style="margin-bottom: 4px;">Auto Certification</h4>
                <p style="font-size: 12px; color: #64748b;">Instant verified digital certificates for successful candidates.</p>
            </div>
        </div>

        <div style="background: #f8fafc; padding: 25px; border-radius: 18px; border: 1px dashed #cbd5e1; display: flex; justify-content: center; align-items: center;">
             <a href="registerformcreate.php" style="color: var(--text-dark); text-decoration: none; font-size: 13px; font-weight: 700;">
                Join CheckNow Community →
             </a>
        </div>

    </div>

</section>

                <section id="conduct" class="content-section">
                    <div style="display:flex;justify-content:space-between;margin-bottom:20px; align-items: center;">
                        <h2>Active Assessments</h2>
                        <a href="exam/exam_history.php" class="history-btn"><i
                                class="fa-solid fa-clock-rotate-left"></i> History</a>
                    </div>
                    <div class="exam-grid">
                        <?php
                        $exams = $conn->query("SELECT * FROM tests WHERE status='Active' AND id NOT IN (SELECT test_id FROM test_attempts WHERE student_id='$user_id' AND status='Completed')");
                        if ($exams && $exams->num_rows > 0) {
                            while ($ex = $exams->fetch_assoc()) { ?>
                                <div class="exam-card">
                                    <div>
                                        <span class="category-pill"><?php echo htmlspecialchars($ex['category']); ?></span>
                                        <h3 style="color:#0f172a; margin-bottom: 8px; font-size: 20px;">
                                            <?php echo htmlspecialchars($ex['test_name']); ?>
                                        </h3>
                                        <p style="font-size: 14px; color: #94a3b8;"><i class="fa-regular fa-clock"></i>
                                            <?php echo $ex['duration']; ?> Minutes</p>
                                    </div>
                                    <button onclick="verifyAndStart(<?php echo $ex['id']; ?>)" class="btn-start"
                                        style="margin-top:20px;">Join Room</button>
                                </div>
                            <?php }
                        } else {
                            echo "<div style='text-align:center; width:100%; padding: 50px; color:#94a3b8;'><i class='fa-solid fa-folder-open' style='font-size: 40px; margin-bottom: 10px;'></i><p>No exams available.</p></div>";
                        } ?>
                    </div>
                </section>

                <section id="questionbank" class="content-section">
                    <h2 style="margin-bottom:20px;">Question Bank</h2>
                    <table>
                        <tr>
                            <th>Bank</th>
                            <th>Category</th>
                            <th>Questions</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        $banks = $conn->query("SELECT t.id,t.test_name,t.category,COUNT(q.id) as total_q FROM tests t LEFT JOIN questions q ON t.id=q.test_id WHERE t.status='Active' GROUP BY t.id");
                        while ($row = $banks->fetch_assoc()) { ?>
                            <tr>
                                <td style="font-weight:600;"><?php echo htmlspecialchars($row['test_name']); ?></td>
                                <td><span class="category-pill"
                                        style="margin:0;"><?php echo htmlspecialchars($row['category']); ?></span></td>
                                <td><?php echo $row['total_q']; ?> Items</td>
                                <td><a href="question_bank_testers.php?test_id=<?php echo $row['id']; ?>"
                                        style="color:var(--accent-blue); text-decoration:none; font-weight:600;">View Bank
                                        →</a></td>
                            </tr>
                        <?php } ?>
                    </table>
                </section>
                <!-- result Analyze -->
                <section id="analyze" class="content-section">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                        <div>
                            <h1 style="font-size: 30px; color: #0f172a; margin: 0;">Deep Analytics</h1>
                            <p style="color: #64748b; margin-top: 5px;">Comprehensive breakdown of your assessment
                                history and performance trends.</p>
                        </div>
                        <div style="text-align: right;">
                            <span
                                style="display: block; font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Overall
                                Average</span>
                            <span
                                style="font-size: 24px; font-weight: 800; color: var(--accent-red);"><?php echo $avg_score; ?>%</span>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <?php
                        $results = $conn->query("
            SELECT t.test_name, t.category, ta.score, ta.passed, ta.completed_at, ta.id as attempt_id 
            FROM test_attempts ta 
            JOIN tests t ON ta.test_id=t.id 
            WHERE ta.student_id='$user_id' AND ta.status = 'Completed' 
            ORDER BY ta.completed_at DESC
        ");

                        if ($results && $results->num_rows > 0) {
                            while ($row = $results->fetch_assoc()) {
                                $p_color = $row['passed'] ? '#22c55e' : '#ef4444';
                                $p_bg = $row['passed'] ? '#f0fdf4' : '#fef2f2';
                                $score_val = round($row['score']);
                                ?>
                                <div
                                    style="background: #fff; border: 1px solid #f1f5f9; border-radius: 24px; padding: 25px; display: grid; grid-template-columns: 1fr 150px 180px; align-items: center; transition: 0.3s; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">

                                    <div style="display: flex; align-items: center; gap: 20px;">
                                        <div
                                            style="width: 50px; height: 50px; border-radius: 15px; background: #f8fafc; display: flex; align-items: center; justify-content: center; color: var(--accent-blue); border: 1px solid #e2e8f0;">
                                            <i class="fa-solid fa-layer-group"></i>
                                        </div>
                                        <div>
                                            <span class="category-pill"
                                                style="margin: 0; padding: 4px 10px; font-size: 10px;"><?php echo htmlspecialchars($row['category']); ?></span>
                                            <h3 style="font-size: 18px; color: #0f172a; margin-top: 5px;">
                                                <?php echo htmlspecialchars($row['test_name']); ?>
                                            </h3>
                                            <p style="font-size: 12px; color: #94a3b8; margin-top: 3px;">
                                                <i class="fa-regular fa-calendar-check"></i>
                                                <?php echo date('M d, Y • H:i', strtotime($row['completed_at'])); ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div style="text-align: center;">
                                        <div style="position: relative; display: inline-block;">
                                            <svg width="60" height="60" viewBox="0 0 60 60">
                                                <circle cx="30" cy="30" r="25" fill="none" stroke="#f1f5f9" stroke-width="5" />
                                                <circle cx="30" cy="30" r="25" fill="none"
                                                    stroke="<?php echo $score_val > 50 ? 'var(--accent-blue)' : 'var(--accent-red)'; ?>"
                                                    stroke-width="5" stroke-dasharray="157"
                                                    stroke-dashoffset="<?php echo 157 - (157 * $score_val / 100); ?>"
                                                    stroke-linecap="round" style="transition: 1s ease-out;" />
                                            </svg>
                                            <div
                                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 13px; font-weight: 800; color: #0f172a;">
                                                <?php echo $score_val; ?>%
                                            </div>
                                        </div>
                                        <span
                                            style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-top: 5px; text-transform: uppercase;">Accuracy</span>
                                    </div>

                                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                                        <span
                                            style="background: <?php echo $p_bg; ?>; color: <?php echo $p_color; ?>; padding: 6px 16px; border-radius: 100px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                            <i
                                                class="fa-solid <?php echo $row['passed'] ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
                                            <?php echo $row['passed'] ? 'Passed' : 'Failed'; ?>
                                        </span>
                                        <a href="exam/exam_results.php?attempt_id=<?php echo $row['attempt_id']; ?>"
                                            style="font-size: 13px; color: var(--accent-blue); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                                            View Breakdown <i class="fa-solid fa-arrow-right-long" style="font-size: 10px;"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <div
                                style="text-align:center; padding: 80px; background: #fff; border-radius: 30px; border: 2px dashed #e2e8f0;">
                                <i class="fa-solid fa-magnifying-glass-chart"
                                    style="font-size: 50px; color: #cbd5e1; margin-bottom: 20px;"></i>
                                <h3 style="color: #64748b;">No analytics data found.</h3>
                                <p style="color: #94a3b8; font-size: 14px;">Complete your first assessment to unlock deep
                                    insights.</p>
                            </div>
                        <?php } ?>
                    </div>
                </section>


                <section id="certificates" class="content-section">
                    <h2 style="margin-bottom: 20px;">Certificates</h2>
                    <div class="exam-grid">
                        <?php
                        $cert = $conn->query("SELECT t.test_name, ta.score, ta.completed_at, ta.id attempt_id FROM test_attempts ta JOIN tests t ON ta.test_id=t.id WHERE ta.student_id='$user_id' AND ta.passed=1");
                        if ($cert && $cert->num_rows > 0) {
                            while ($row = $cert->fetch_assoc()) { ?>
                                <div class="exam-card">
                                    <div><i class="fa-solid fa-certificate"
                                            style="color:gold; font-size:30px; margin-bottom:10px;"></i>
                                        <h3><?php echo htmlspecialchars($row['test_name']); ?></h3>
                                        <p style="font-size:12px;color:#94a3b8; margin-top:5px;">Completed:
                                            <?php echo date('d M Y', strtotime($row['completed_at'])); ?>
                                        </p>
                                    </div>
                                    <a href="exam/certificate.php?attempt_id=<?php echo $row['attempt_id']; ?>" target="_blank"
                                        style="margin-top:20px; text-decoration:none; text-align:center; background:var(--accent-blue); color:white; padding:12px; border-radius:12px; font-weight:600;">Download
                                        PDF</a>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </section>

                <section id="monitor" class="content-section">
                    <h1>Monitoring System</h1>
                </section>
            </main>
        </div>
    </div>

    <div id="examModal" class="modal">
        <div class="modal-box">
            <i class="fa-solid fa-shield-halved"
                style="font-size: 40px; color: var(--accent-blue); margin-bottom: 15px;"></i>
            <h3>Identity Check</h3>
            <input type="password" id="examPassword" placeholder="••••••••">
            <div class="modal-buttons">
                <button onclick="submitExamPassword()" class="btn-confirm">Unlock Exam</button>
                <button onclick="closeModal()"
                    style="flex:1; background:#f1f5f9; border:none; border-radius:12px; font-weight:600; cursor:pointer;">Cancel</button>
            </div>
        </div>
    </div>

    <script>

        /* ================= SIDEBAR NAVIGATION ================= */

        document.querySelectorAll(".menu-link").forEach(link => {

            link.addEventListener("click", function () {

                document.querySelectorAll(".menu-link").forEach(l =>
                    l.classList.remove("active")
                );

                document.querySelectorAll(".content-section").forEach(s =>
                    s.classList.remove("active")
                );

                this.classList.add("active");

                const target = document.getElementById(
                    this.getAttribute("data-section")
                );

                if (target) target.classList.add("active");

            });

        });


        /* ================= LOAD SECTION FROM URL ================= */

        document.addEventListener("DOMContentLoaded", function () {

            let params = new URLSearchParams(window.location.search);

            let section = params.get("section");

            if (section) {

                document.querySelectorAll(".menu-link").forEach(l =>
                    l.classList.remove("active")
                );

                document.querySelectorAll(".content-section").forEach(s =>
                    s.classList.remove("active")
                );

                let menu = document.querySelector(`[data-section="${section}"]`);
                let content = document.getElementById(section);

                if (menu && content) {
                    menu.classList.add("active");
                    content.classList.add("active");
                }

            }

        });


        /* ================= EXAM MODAL LOGIC ================= */

        let selectedExam = null;


        /* Open modal */
        function verifyAndStart(testId) {

            selectedExam = testId;

            document.getElementById("examModal").style.display = "flex";

            document.getElementById("examPassword").focus();

        }


        /* Close modal */
        function closeModal() {

            document.getElementById("examModal").style.display = "none";

            document.getElementById("examPassword").value = "";

        }


        /* Submit password */
        function submitExamPassword() {

            let pass = document.getElementById("examPassword").value.trim();

            if (pass === "") {
                alert("Password required");
                return;
            }

            const form = document.createElement("form");

            form.method = "POST";

            form.action = "exam/start_engine.php";


            const idInput = document.createElement("input");
            idInput.type = "hidden";
            idInput.name = "test_id";
            idInput.value = selectedExam;


            const passInput = document.createElement("input");
            passInput.type = "hidden";
            passInput.name = "verify_pass";
            passInput.value = pass;


            form.appendChild(idInput);
            form.appendChild(passInput);

            document.body.appendChild(form);

            form.submit();

        }


        /* ================= CLOSE MODAL ON OUTSIDE CLICK ================= */

        window.onclick = function (event) {

            let modal = document.getElementById("examModal");

            if (event.target === modal) {

                closeModal();

            }

        };

    </script>
</body>

</html>