<?php
require '../config.php';

/* SAFE SESSION START */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Check login */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Fetch completed exam history */
$history = $conn->query("
SELECT t.test_name, t.category, ta.score, ta.passed, ta.completed_at
FROM test_attempts ta
JOIN tests t ON ta.test_id = t.id
WHERE ta.student_id='$user_id' AND ta.status='Completed'
ORDER BY ta.completed_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <title>My History | CHECKNOW</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            padding: 50px;
            margin: 0;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 24px;
            max-width: 1000px;
            margin: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 15px;
            background: #f8fafc;
            border-bottom: 2px solid #f1f5f9;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid #f1f5f9;
            color: #0f172a;
            font-size: 15px;
        }

        .status {
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pass {
            background: #dcfce7;
            color: #15803d;
        }

        .fail {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* BACK BUTTON */

        .back-btn {
            text-decoration: none;
            color: #3b82f6;
            font-weight: 600;
            font-size: 14px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            transform: translateX(-5px);
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="header">

            <div>
                <h2 style="margin:0;">My Exam History</h2>
                <p style="color:#64748b;font-size:14px;margin-top:5px;">
                    Review your previous performance results.
                </p>
            </div>

            <!-- FIXED BACK BUTTON -->

            <a href="../makeit.php?section=conduct#conduct" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i> Back to Conduct Exams
            </a>

        </div>

        <table>

            <thead>

                <tr>
                    <th>Exam Name</th>
                    <th>Category</th>
                    <th>Score</th>
                    <th>Status</th>
                    <th>Completed On</th>
                </tr>

            </thead>

            <tbody>

                <?php if ($history && $history->num_rows > 0): ?>

                    <?php while ($row = $history->fetch_assoc()): ?>

                        <tr>

                            <td style="font-weight:600;">
                                <?php echo htmlspecialchars($row['test_name']); ?>
                            </td>

                            <td>
                                <span style="color:#64748b;">
                                    <?php echo htmlspecialchars($row['category']); ?>
                                </span>
                            </td>

                            <td style="font-weight:700;color:#3b82f6;">
                                <?php echo round($row['score']); ?>%
                            </td>

                            <td>
                                <span class="status <?php echo $row['passed'] ? 'pass' : 'fail'; ?>">
                                    <?php echo $row['passed'] ? 'Passed' : 'Failed'; ?>
                                </span>
                            </td>

                            <td style="color:#94a3b8;font-size:13px;">
                                <?php echo date('M d, Y', strtotime($row['completed_at'])); ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;color:#94a3b8;">
                            No exam records found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</body>

</html>