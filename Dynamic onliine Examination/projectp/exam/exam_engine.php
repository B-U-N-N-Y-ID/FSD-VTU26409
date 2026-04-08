<?php
require_once '../config.php';

if (session_status() === PHP_SESSION_NONE)
    session_start();

// Security check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['current_test_id'])) {
    header("Location: ../makeit.php?section=conduct");
    exit();
}

$user_id = $_SESSION['user_id'];
$test_id = $_SESSION['current_test_id'];

$test_stmt = $conn->prepare("SELECT duration, test_name FROM tests WHERE id = ?");
$test_stmt->bind_param("i", $test_id);
$test_stmt->execute();
$test = $test_stmt->get_result()->fetch_assoc();

$q_stmt = $conn->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY id ASC");
$q_stmt->bind_param("i", $test_id);
$q_stmt->execute();
$questions = $q_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (!$test || empty($questions)) {
    die("Assessment data not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Exam Engine | CHECKNOW</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #ffa500;
            --dark: #1e293b;
            --blue: #3b82f6;
            --green: #10b981;
            --red: #ef4444;
            --purple: #8b5cf6;
            --bg: #f8fafc;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            user-select: none;
            overflow: hidden;
        }

        header {
            height: 70px;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            border-bottom: 2px solid #e2e8f0;
            position: relative;
            z-index: 10;
        }

        .timer {
            background: var(--primary);
            color: white;
            padding: 10px 25px;
            border-radius: 12px;
            font-weight: 700;
            font-family: monospace;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(255, 165, 0, 0.2);
            transition: 0.3s;
        }

        .timer.warning {
            background: var(--red);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .main {
            display: grid;
            grid-template-columns: 300px 1fr;
            height: calc(100vh - 130px);
        }

        .sidebar {
            background: var(--dark);
            color: white;
            padding: 25px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
        }

        .stats {
            background: rgba(15, 23, 42, 0.6);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            font-size: 13px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stats div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .legend {
            font-size: 11px;
            margin-bottom: 20px;
            padding: 0 5px;
        }

        .legend div {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            color: #94a3b8;
        }

        .legend span {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 3px;
            margin-right: 12px;
        }

        .q-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .q-btn {
            height: 42px;
            border-radius: 10px;
            border: 2px solid transparent;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            font-weight: 600;
            transition: 0.3s;
        }

        .q-btn.active {
            border-color: var(--primary) !important;
            box-shadow: 0 0 10px rgba(255, 165, 0, 0.5);
        }

        .q-btn.visited {
            background: var(--red);
            color: white;
        }

        .q-btn.answered {
            background: var(--green);
            color: white;
        }

        .q-btn.marked {
            background: var(--purple);
            color: white;
            border-radius: 50%;
        }

        .content {
            padding: 50px;
            overflow: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .progress {
            height: 10px;
            background: #e2e8f0;
            border-radius: 20px;
            margin-bottom: 40px;
            width: 100%;
            max-width: 850px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: var(--green);
            width: 0%;
            border-radius: 20px;
            transition: 0.5s ease-out;
        }

        .card {
            background: white;
            padding: 50px;
            border-radius: 24px;
            max-width: 850px;
            width: 100%;
            display: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card.active {
            display: block;
        }

        .options {
            margin-top: 35px;
        }

        .option {
            padding: 20px 25px;
            border: 2px solid #f1f5f9;
            border-radius: 16px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
            color: #334155;
        }

        .option:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .option.selected {
            border-color: var(--primary);
            background: #fffaf0;
            color: var(--dark);
        }

        .footer-actions {
            height: 60px;
            background: white;
            border-top: 2px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            position: fixed;
            bottom: 0;
            width: 100%;
            box-sizing: border-box;
            z-index: 100;
        }

        .btn {
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid #ddd;
            transition: 0.2s;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--dark);
            color: white;
            border: none;
        }

        .btn-finish {
            background: var(--primary);
            color: white;
            border: none;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>

<body oncontextmenu="return false;">

    <header>
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fa-solid fa-graduation-cap" style="color:var(--primary); font-size:24px;"></i>
            <h3 style="margin:0; font-weight:700; color:var(--dark);">CHECKNOW |
                <?php echo htmlspecialchars($test['test_name']); ?>
            </h3>
        </div>
        <div class="timer" id="timer">--:--</div>
    </header>

    <div class="main">
        <div class="sidebar">
            <div class="stats">
                <div><span>Total Items</span><span><?php echo count($questions); ?></span></div>
                <div><span style="color:var(--green)">Answered</span><span id="answered">0</span></div>
                <div><span style="color:var(--purple)">Marked</span><span id="markedCount">0</span></div>
                <div><span style="color:var(--red)">Not Answered</span><span
                        id="notAnswered"><?php echo count($questions); ?></span></div>
            </div>

            <div class="legend">
                <div><span style="background:var(--green)"></span> Answered</div>
                <div><span style="background:var(--purple); border-radius:50%"></span> Marked</div>
                <div><span style="background:var(--red)"></span> Visited</div>
            </div>

            <div class="q-grid">
                <?php foreach ($questions as $i => $q) { ?>
                    <button class="q-btn" id="btn<?php echo $i; ?>"
                        onclick="gotoQ(<?php echo $i; ?>)"><?php echo $i + 1; ?></button>
                <?php } ?>
            </div>
        </div>

        <div class="content">
            <div class="progress">
                <div class="progress-bar" id="progress"></div>
            </div>
            <form id="examForm">
                <?php foreach ($questions as $i => $q) { ?>
                    <div class="card" id="q<?php echo $i; ?>">
                        <h4>Item <?php echo $i + 1; ?></h4>
                        <h3 style="font-size:24px; line-height:1.5; font-weight:600;">
                            <?php echo htmlspecialchars($q['question']); ?>
                        </h3>
                        <div class="options">
                            <?php foreach (['a', 'b', 'c', 'd'] as $opt) { ?>
                                <label class="option">
                                    <input type="radio" name="q<?php echo $i; ?>" value="<?php echo strtoupper($opt); ?>"
                                        onclick="answerQ(<?php echo $i; ?>)" style="accent-color: var(--primary);">
                                    <?php echo htmlspecialchars($q['option_' . $opt]); ?>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div>

    <div class="footer-actions">
        <div style="display:flex; gap:10px;">
            <button class="btn" onclick="markReview()">Mark for Review & Next</button>
            <button class="btn" onclick="clearResponse()">Clear Response</button>
        </div>
        <div style="display:flex; gap:10px;">
            <button class="btn btn-primary" onclick="nextQ()">Save & Next</button>
            <button class="btn btn-finish" id="finishBtn" onclick="submitExam()">Final Submission</button>
        </div>
    </div>

    <script>
        let current = 0;
        let total = <?php echo count($questions); ?>;
        let answeredSet = new Set();
        let markedSet = new Set();

        window.onbeforeunload = function () { return "Progress will be lost!"; };

        function showQ(i) {
            // visited logic: turn RED if visited but not saved/answered
            if (i !== current && !answeredSet.has(current) && !markedSet.has(current)) {
                document.getElementById('btn' + current).classList.add('visited');
            }
            document.querySelectorAll('.card').forEach(c => c.classList.remove('active'));
            document.getElementById('q' + i).classList.add('active');
            document.querySelectorAll('.q-btn').forEach(b => b.classList.remove('active'));
            const btn = document.getElementById('btn' + i);
            btn.classList.add('active');
            current = i;
            updateStats();
        }

        function nextQ() {
            const selected = document.querySelector(`input[name="q${current}"]:checked`);
            if (selected) {
                answeredSet.add(current);
                markedSet.delete(current);
                document.getElementById('btn' + current).className = 'q-btn answered';
            } else {
                answeredSet.delete(current);
                if (!markedSet.has(current)) document.getElementById('btn' + current).className = 'q-btn visited';
            }
            if (current < total - 1) showQ(current + 1);
            else updateStats();
        }

        function markReview() {
            markedSet.add(current);
            answeredSet.delete(current);
            document.getElementById('btn' + current).className = 'q-btn marked';
            if (current < total - 1) showQ(current + 1);
            else updateStats();
        }

        function clearResponse() {
            document.querySelectorAll(`input[name="q${current}"]`).forEach(r => r.checked = false);
            document.querySelectorAll(`#q${current} .option`).forEach(o => o.classList.remove('selected'));
            answeredSet.delete(current);
            markedSet.delete(current);
            document.getElementById('btn' + current).className = 'q-btn visited active';
            updateStats();
        }

        function answerQ(i) {
            const container = document.getElementById('q' + i);
            container.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
            const selected = document.querySelector(`input[name="q${i}"]:checked`);
            if (selected) {
                selected.parentElement.classList.add('selected');
                answeredSet.add(i);
                markedSet.delete(i);
                document.getElementById('btn' + i).className = 'q-btn answered active';
            }
            updateStats();
        }

        function gotoQ(i) { showQ(i); }

        function updateStats() {
            document.getElementById('answered').innerText = answeredSet.size;
            document.getElementById('markedCount').innerText = markedSet.size;
            document.getElementById('notAnswered').innerText = total - answeredSet.size;
            document.getElementById('progress').style.width = (answeredSet.size / total * 100) + "%";
        }

        let timeRemaining = <?php echo $test['duration']; ?> * 60;
        const timerInterval = setInterval(() => {
            let m = Math.floor(timeRemaining / 60);
            let s = timeRemaining % 60;
            document.getElementById('timer').innerHTML = `${m}:${s < 10 ? "0" : ""}${s}`;
            if (timeRemaining <= 60) document.getElementById('timer').classList.add('warning');
            if (timeRemaining-- <= 0) { clearInterval(timerInterval); submitExam(); }
        }, 1000);

        
        function submitExam() {

        window.onbeforeunload = null;
            let answers = [];

            for (let i = 0; i < total; i++) {
                let s = document.querySelector(`input[name="q${i}"]:checked`);
                answers.push(s ? s.value : null);
            }

            document.getElementById('finishBtn').innerHTML =
                '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';

            fetch("submit_processor.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    test_id: <?php echo $test_id; ?>,
                    answers: answers
                })
            })
                .then(r => r.json())
                .then(d => {

                    console.log(d);

                    if (d.status === "success") {
                        if (d.attempt_id) {
                            window.location.href = "exam_results.php?attempt_id=" + d.attempt_id;
                        } else {
                            window.location.href = "exam_results.php";
                        }
                    }

                });
        }
        document.addEventListener("DOMContentLoaded", function () {
            showQ(0);
        });
    </script>
</body>

</html>