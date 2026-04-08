<?php
require_once '../config.php';
$user = $_SESSION['user_id'];
$d = $conn->query("SELECT t.test_name, ta.score FROM test_attempts ta JOIN tests t ON ta.test_id=t.id WHERE ta.student_id='$user'");
?>
<!DOCTYPE html>
<html>

<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 50px;
            background: #f8fafc;
        }

        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="chart-container">
        <h2 style="margin-bottom:30px">📈 Performance Analytics</h2>
        <canvas id="chart"></canvas>
        <button onclick="history.back()"
            style="margin-top:20px; border:none; background:none; color:#3b82f6; cursor:pointer; font-weight:600">← Back
            to Results</button>
    </div>
    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php while ($r = $d->fetch_assoc())
                    echo "'" . $r['test_name'] . "',"; ?>],
                datasets: [{
                    label: 'Scores (%)',
                    data: [<?php $d->data_seek(0);
                    while ($r = $d->fetch_assoc())
                        echo $r['score'] . ","; ?>],
                    backgroundColor: '#3b82f6',
                    borderRadius: 8
                }]
            },
            options: { scales: { y: { beginAtZero: true, max: 100 } } }
        });
    </script>
</body>

</html>