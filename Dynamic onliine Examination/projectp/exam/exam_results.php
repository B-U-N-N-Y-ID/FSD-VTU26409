<?php
require_once '../config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?login_required=1");
    exit();
}
// Support viewing specific attempts from URL or current session
$id = $_GET['attempt_id'] ?? $_SESSION['attempt_id'] ?? null;

if (!$id) {
    header("Location: ../makeit.php?section=conduct");
    exit();
}

// Fetch dynamic exam counts and attempt details
$stmt = $conn->prepare("
    SELECT ta.*, t.test_name, t.category,
    (SELECT COUNT(*) FROM questions WHERE test_id = t.id) as total_q
    FROM test_attempts ta 
    JOIN tests t ON ta.test_id = t.id 
    WHERE ta.id = ?
");
$stmt->bind_param("s", $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();

if (!$r) {
    header("Location: ../makeit.php?section=conduct");
    exit();
}

$score = round($r['score']);
$isPassed = (bool) $r['passed'];
$totalQuestions = $r['total_q'];
$correctCount = round(($score / 100) * $totalQuestions);
$wrongCount = $totalQuestions - $correctCount;

/* --- CIRCLE CALCULATIONS --- */
$radius = 95;
$circumference = 2 * pi() * $radius;
$offset = $circumference - ($score / 100) * $circumference;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Performance Analysis | CHECKNOW</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            /* VIVID PURPLE & TEAL PALETTE */
            --primary-purple: #7c3aed;
            --light-purple: #f5f3ff;
            --vibrant-teal: #0d9488;
            --soft-teal: #f0fdfa;
            --dark-navy: #0f172a;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f8fafc;
            background-image: radial-gradient(circle at 10% 20%, rgba(124, 58, 237, 0.05) 0%, transparent 90%),
                radial-gradient(circle at 90% 80%, rgba(13, 148, 136, 0.05) 0%, transparent 90%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .hybrid-card {
            background: white;
            width: 100%;
            max-width: 950px;
            display: grid;
            grid-template-columns: 380px 1fr;
            border-radius: 40px;
            overflow: hidden;
            box-shadow: 0 40px 100px -20px rgba(124, 58, 237, 0.15);
            border: 1px solid #fff;
        }

        /* --- SIDEBAR GAUGE --- */
        .gauge-sidebar {
            background: linear-gradient(180deg, var(--dark-navy) 0%, #1e1b4b 100%);
            color: white;
            padding: 60px 40px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .gauge-sidebar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, var(--primary-purple), transparent 70%);
            opacity: 0.15;
        }

        .score-circle {
            position: relative;
            width: 220px;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px;
            z-index: 1;
        }

        .score-circle svg {
            transform: rotate(-90deg);
            width: 220px;
            height: 220px;
        }

        .score-circle circle {
            fill: none;
            stroke-width: 14;
            stroke-linecap: round;
        }

        .circle-bg {
            stroke: rgba(255, 255, 255, 0.08);
        }

        .circle-progress {
            stroke: var(--primary-purple);
            stroke-dasharray:
                <?php echo $circumference; ?>
            ;
            stroke-dashoffset:
                <?php echo $circumference; ?>
            ;
            transition: stroke-dashoffset 2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            filter: drop-shadow(0 0 12px var(--primary-purple));
        }

        .score-content {
            position: absolute;
            display: flex;
            flex-direction: column;
            z-index: 2;
        }

        .score-val {
            font-size: 70px;
            font-weight: 800;
            line-height: 1;
            color: #fff;
        }

        .score-of {
            font-size: 14px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .pass-status {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
            z-index: 1;
        }

        .status-txt {
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.6;
            z-index: 1;
        }

        /* --- DATA PANEL --- */
        .data-panel {
            padding: 60px;
            background: white;
        }

        .module-badge {
            background: var(--light-purple);
            color: var(--primary-purple);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            display: inline-block;
        }

        .exam-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--dark-navy);
            margin-bottom: 35px;
        }

        .data-row {
            background: #f8fafc;
            padding: 25px;
            border-radius: 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #f1f5f9;
        }

        .data-label {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 700;
            color: #475569;
        }

        .data-icon {
            width: 45px;
            height: 45px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            color: var(--primary-purple);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.1);
        }

        .progress-bar {
            width: 120px;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            margin: 0 20px;
            overflow: hidden;
        }

        .fill {
            height: 100%;
            background: var(--primary-purple);
            border-radius: 100px;
            transition: 1.5s ease-in-out;
        }

        .answer-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px dashed #f1f5f9;
        }

        .stat-item {
            text-align: center;
        }

        .stat-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .stat-circle {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--dark-navy);
            font-size: 18px;
            background: #f1f5f9;
            transition: 0.3s;
        }

        .action-footer {
            margin-top: 50px;
            display: grid;
            gap: 15px;
        }

        .btn {
            padding: 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            transition: 0.3s;
            font-size: 15px;
        }

        .btn-cert {
            background: linear-gradient(135deg, var(--primary-purple), #6366f1);
            color: white;
            box-shadow: 0 15px 30px rgba(124, 58, 237, 0.3);
        }

        .btn-cert:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(124, 58, 237, 0.4);
        }

        .btn-dash {
            background: var(--dark-navy);
            color: white;
        }

        @media (max-width: 850px) {
            .hybrid-card {
                grid-template-columns: 1fr;
            }

            .gauge-sidebar {
                padding: 40px;
            }
        }
    </style>
</head>

<body onload="startAnimation()">

    <div class="hybrid-card">
        <div class="gauge-sidebar">
            <span class="score-of">Analysis Range</span>
            <div class="score-circle">
                <svg>
                    <circle class="circle-bg" cx="110" cy="110" r="95"></circle>
                    <circle id="progressCircle" class="circle-progress" cx="110" cy="110" r="95"></circle>
                </svg>
                <div class="score-content">
                    <span class="score-val"><?php echo $score; ?></span>
                    <span class="score-of">Points</span>
                </div>
            </div>

            <h2 class="pass-status" style="color: <?php echo $isPassed ? 'var(--vibrant-teal)' : '#f87171'; ?>;">
                <?php echo $isPassed ? 'Victory!' : 'Keep Going'; ?>
            </h2>
            <p class="status-txt">
                <?php echo $isPassed
                    ? 'Assessment successfully verified. Your official certification is now available.'
                    : 'Target score not met. Review the categorical data below to prepare for retry.'; ?>
            </p>
        </div>

        <div class="data-panel">
            <span class="module-badge"><?php echo htmlspecialchars($r['category']); ?></span>
            <h1 class="exam-title"><?php echo htmlspecialchars($r['test_name']); ?></h1>

            <div class="data-row">
                <div class="data-label">
                    <div class="data-icon"><i class="fa-solid fa-chart-line"></i></div>
                    Accuracy Metrics
                </div>
                <div style="display:flex; align-items:center;">
                    <div class="progress-bar">
                        <div class="fill" style="width: <?php echo $score; ?>%;"></div>
                    </div>
                    <span
                        style="font-weight: 800; color: var(--dark-navy); font-size: 18px;"><?php echo $score; ?>%</span>
                </div>
            </div>

            <div class="answer-stats">
                <div class="stat-item">
                    <p class="stat-label">Correct</p>
                    <div class="stat-circle" style="background: var(--soft-teal); color: var(--vibrant-teal);">
                        <?php echo $correctCount; ?>
                    </div>
                </div>
                <div class="stat-item">
                    <p class="stat-label">Incorrect</p>
                    <div class="stat-circle" style="background: #fff1f2; color: #f43f5e;">
                        <?php echo $wrongCount; ?>
                    </div>
                </div>
                <div class="stat-item">
                    <p class="stat-label">Total Qs</p>
                    <div class="stat-circle">
                        <?php echo $totalQuestions; ?>
                    </div>
                </div>
            </div>

            <div class="action-footer">
                <?php if ($isPassed): ?>
                    <a href="certificate.php?attempt_id=<?php echo $id; ?>" class="btn btn-cert">
                        <i class="fa-solid fa-award"></i> View My Certificate
                    </a>
                <?php endif; ?>

                <a href="../makeit.php?section=conduct" class="btn btn-dash">
                    <i class="fa-solid fa-house"></i> Return to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        function startAnimation() {
            const circle = document.getElementById('progressCircle');
            circle.style.strokeDashoffset = '<?php echo $offset; ?>';
        }
    </script>

</body>

</html>