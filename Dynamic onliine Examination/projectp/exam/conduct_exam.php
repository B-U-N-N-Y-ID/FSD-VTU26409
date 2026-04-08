<?php
require_once '../config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?login_required=1");
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch Active Exams
$exams = $conn->query("SELECT t.*, u.first_name creator FROM tests t JOIN users u ON t.creator_id=u.id WHERE t.status='Active' AND t.id NOT IN(SELECT test_id FROM test_attempts WHERE student_id='$user_id' AND status='Completed')");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --dark: #0f172a;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            color: var(--dark);
            padding: 40px;
            margin: 0;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .exam-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .exam-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .exam-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        .btn-join {
            background: var(--primary);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }

        /* REAL-TIME MODAL UI */
        #verifyModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-card {
            background: white;
            padding: 40px;
            border-radius: 28px;
            width: 400px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #f1f5f9;
            border-radius: 14px;
            margin: 25px 0;
            outline: none;
            font-size: 18px;
            text-align: center;
            transition: 0.3s;
        }

        .modal-input:focus {
            border-color: var(--primary);
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Available Assessments</h2>
        <div class="exam-grid">
            <?php while ($e = $exams->fetch_assoc()) { ?>
                <div class="exam-card">
                    <h3><?php echo htmlspecialchars($e['test_name']); ?></h3>
                    <p style="color:#64748b; font-size:14px;">Time: <?php echo $e['duration']; ?> mins</p>
                    <button class="btn-join" onclick="openGate(<?php echo $e['id']; ?>)">Join Room</button>
                </div>
            <?php } ?>
        </div>
    </div>

    <div id="verifyModal">
        <div class="modal-card">
            <div
                style="width:70px; height:70px; background:#eff6ff; color:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:30px;">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h3 style="margin:0;">Identity Check</h3>
            <p style="color:#64748b; font-size:14px; margin-top:8px;">Enter your account password to verify.</p>

            <form action="start_engine.php" method="POST">
                <input type="hidden" name="test_id" id="gate_id">
                <input type="password" name="verify_pass" class="modal-input" placeholder="••••••••" required autofocus>
                <div style="display:flex; gap:10px;">
                    <button type="button" onclick="closeGate()"
                        style="flex:1; background:#f1f5f9; color:#64748b; padding:12px; border:none; border-radius:12px; cursor:pointer; font-weight:600;">Cancel</button>
                    <button type="submit"
                        style="flex:2; background:var(--dark); color:white; padding:12px; border:none; border-radius:12px; cursor:pointer; font-weight:600;">Verify
                        Access</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openGate(id) {
            document.getElementById('gate_id').value = id;
            document.getElementById('verifyModal').style.display = 'flex';
        }
        function closeGate() {
            document.getElementById('verifyModal').style.display = 'none';
        }
    </script>
</body>

</html>