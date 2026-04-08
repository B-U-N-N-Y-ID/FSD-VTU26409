<?php
require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$attempt = $_GET['attempt_id'] ?? null;

$stmt = $conn->prepare("
SELECT u.first_name, u.last_name, t.test_name, ta.score, ta.completed_at,
c.first_name AS creator_first, c.last_name AS creator_last
FROM test_attempts ta
JOIN users u ON ta.student_id = u.id
JOIN tests t ON ta.test_id = t.id
JOIN users c ON t.creator_id = c.id
WHERE ta.id = ?
");

$stmt->bind_param("i", $attempt);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();

if (!$r) {
    die("Certificate not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Official Credential | CHECKNOW</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&family=Playfair+Display:wght@700&family=Dancing+Script:wght@700&display=swap"
        rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        :root {
            --neon-purple: #a855f7;
            --deep-midnight: #020617;
            --gold: #c9a227;
        }

        body {
            margin: 0;
            background: #111;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Montserrat', sans-serif;
        }

        /* FIXED A4 LANDSCAPE BOX */
        .cert-canvas {
            width: 1123px;
            height: 794px;
            background: var(--deep-midnight);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 50px 100px rgba(0, 0, 0, 0.8);
        }

        /* High-End Background Glows */
        .cert-canvas::before {
            content: '';
            position: absolute;
            bottom: -100px;
            right: -100px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.2), transparent 70%);
        }

        .cert-canvas::after {
            content: '';
            position: absolute;
            top: -100px;
            left: -100px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.15), transparent 70%);
        }

        .inner-frame {
            width: 90%;
            height: 85%;
            margin-top: 50px;
            border: 2px solid rgba(168, 85, 247, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 60px;
            z-index: 10;
            position: relative;
        }

        .header-box h1 {
            font-family: "Playfair Display", serif;
            font-size: 90px;
            color: #fff;
            margin: 0;
            letter-spacing: 5px;
            text-transform: uppercase;
        }

        .subtitle {
            letter-spacing: 12px;
            color: var(--neon-purple);
            text-transform: uppercase;
            font-weight: 700;
            margin-top: 10px;
        }

        .recipient-label {
            margin-top: 50px;
            font-size: 14px;
            letter-spacing: 4px;
            color: #64748b;
            font-weight: 700;
        }

        .student-name {
            font-family: "Dancing Script", cursive;
            font-size: 95px;
            color: #fff;
            margin: 20px 0;
            border-bottom: 3px solid var(--gold);
            padding: 0 60px 10px 60px;
        }

        .achievement-desc {
            text-align: center;
            color: #94a3b8;
            line-height: 1.8;
            max-width: 800px;
            font-size: 20px;
            margin-top: 30px;
        }

        .footer {
            width: 100%;
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 40px;
        }

        .sig-block {
            text-align: center;
            min-width: 250px;
        }

        .sig-font {
            font-family: "Dancing Script", cursive;
            font-size: 36px;
            color: var(--neon-purple);
        }

        .sig-line {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 10px 0;
        }

        /* ACTIONS */
        .toolbar {
            position: fixed;
            bottom: 30px;
            display: flex;
            gap: 20px;
            z-index: 1000;
        }

        .btn {
            padding: 18px 40px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 800;
            color: white;
            text-transform: uppercase;
            transition: 0.3s;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }

        .btn-pdf {
            background: #7c3aed;
        }

        .btn-print {
            background: #ff3b1f;
        }

        .btn:hover {
            transform: translateY(-5px);
            filter: brightness(1.2);
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }

            .toolbar {
                display: none;
            }

            body {
                background: none;
            }

            .cert-canvas {
                box-shadow: none;
                position: absolute;
                top: 0;
                left: 0;
            }
        }
    </style>
</head>

<body>

    <div class="cert-canvas" id="captureTarget">
        <div class="inner-frame">
            <div class="header-box">
                <h1>Certificate</h1>
                <div class="subtitle">Achievement Excellence</div>
            </div>

            <p class="recipient-label">PROUDLY PRESENTED TO</p>

            <div class="student-name">
                <?php echo htmlspecialchars($r['first_name'] . " " . $r['last_name']); ?>
            </div>

            <p class="achievement-desc">
                For successfully demonstrating exceptional proficiency and successfully completing the
                <b style="color:#fff;">"<?php echo htmlspecialchars($r['test_name']); ?>"</b>
                examination with an outstanding score of <b
                    style="color:var(--neon-purple);"><?php echo round($r['score']); ?>%</b>.
            </p>

            <div class="footer">
                <div class="sig-block">
                    <div class="sig-font">
                        <?php echo htmlspecialchars($r['creator_first'] . " " . $r['creator_last']); ?></div>
                    <div class="sig-line"></div>
                    <span style="font-size:12px; color:#64748b; letter-spacing:2px;">ACADEMIC DIRECTOR</span>
                </div>

                <div
                    style="width:120px; height:120px; border-radius:50%; border:2px dashed var(--neon-purple); display:flex; align-items:center; justify-content:center; color:var(--neon-purple); font-size:40px;">
                    ★
                </div>

                <div class="sig-block">
                    <div style="font-size:22px; font-weight:700; margin-bottom:12px;">
                        <?php echo date('d M Y', strtotime($r['completed_at'])); ?></div>
                    <div class="sig-line"></div>
                    <span style="font-size:12px; color:#64748b; letter-spacing:2px;">DATE OF ISSUANCE</span>
                </div>
            </div>
        </div>
    </div>

    <div class="toolbar">
        <button class="btn btn-pdf" id="pdfBtn" onclick="exportPDF()">
            <i class="fa-solid fa-file-pdf"></i> Official PDF
        </button>
        <button class="btn btn-print" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Print Ready
        </button>
    </div>

    <script>
        async function exportPDF() {
            const btn = document.getElementById("pdfBtn");
            const element = document.getElementById("captureTarget");

            btn.innerHTML = "Processing...";
            btn.disabled = true;

            try {
                // FORCE HIGH-RES RENDER
                const canvas = await html2canvas(element, {
                    scale: 4, // 4x Resolution
                    useCORS: true,
                    backgroundColor: "#020617"
                });

                const imgData = canvas.toDataURL("image/png", 1.0);
                const { jsPDF } = window.jspdf;

                // Sync with A4 Landscape standard
                const pdf = new jsPDF({
                    orientation: 'landscape',
                    unit: 'px',
                    format: [1123, 794]
                });

                pdf.addImage(imgData, 'PNG', 0, 0, 1123, 794, undefined, 'FAST');
                pdf.save("CheckNow_Certificate.pdf");
            } catch (err) {
                console.error(err);
                alert("Render failed. Using Direct Print is recommended.");
            } finally {
                btn.innerHTML = "Official PDF";
                btn.disabled = false;
            }
        }
    </script>
</body>

</html>