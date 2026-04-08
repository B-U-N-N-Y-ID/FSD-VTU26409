<?php
include "header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Knowledge Hub | CHECKNOW</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-orange: #ff4d2f;
            --border: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #fff;
            margin: 0;
            color: var(--text-dark);
        }

        .container {
            width: 1100px;
            margin: auto;
        }

        /* --- SUB-NAVBAR TABS --- */
        .sub-nav {
            border-bottom: 1px solid var(--border);
            background: #fff;
        }

        .sub-nav ul {
            display: flex;
            justify-content: center;
            gap: 50px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .tab-link {
            font-size: 14px;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            padding: 18px 0;
            border-bottom: 3px solid transparent;
            transition: 0.3s;
        }

        .tab-link.active {
            color: var(--primary-orange);
            border-bottom-color: var(--primary-orange);
            font-weight: 700;
        }

        /* --- HERO SEARCH SECTION --- */
        .hero-banner {
            background: #f1f5f9;
            padding: 60px 0;
            border-bottom: 1px solid var(--border);
        }

        .hero-banner h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
        }

        .search-row {
            margin-top: 25px;
            display: flex;
            max-width: 550px;
        }

        .search-row input {
            flex: 1;
            padding: 12px 18px;
            border: 1px solid #ced4da;
            border-radius: 6px 0 0 6px;
            font-size: 14px;
            outline: none;
        }

        .search-row button {
            background: var(--primary-orange);
            color: #fff;
            border: none;
            padding: 0 30px;
            font-weight: 700;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
        }

        /* --- CATEGORIES --- */
        .faq-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 60px;
            padding: 60px 0;
        }

        .faq-col h3 {
            font-size: 18px;
            border-bottom: 2px solid #f8fafc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .faq-col ul {
            list-style: none;
            padding: 0;
        }

        .faq-col li {
            margin-bottom: 12px;
            font-size: 14px;
        }

        .faq-col a {
            color: var(--primary-orange);
            text-decoration: none;
            font-size: 14px;
        }

        .faq-col a:hover {
            text-decoration: underline;
        }

        /* --- USER MANUAL SECTION --- */
        .manual-section {
            padding: 60px 0;
        }

        .manual-header {
            margin-bottom: 30px;
        }

        .manual-download a {
            color: var(--primary-orange);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .system-diagram-container {
            margin-top: 50px;
            text-align: center;
        }

        /* Image Display Settings */
        .system-diagram-container img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        @media (max-width: 900px) {
            .container {
                width: 95%;
            }

            .faq-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <nav class="sub-nav">
        <ul class="container">
            <li class="tab-link active" onclick="switchTab(event,'faq')">FAQ</li>
            <li class="tab-link" onclick="switchTab(event,'manual')">User manual</li>
            <li class="tab-link" onclick="location.href='contact.php'">Video help</li>
            <li class="tab-link">Use cases</li>
        </ul>
    </nav>

    <div class="hero-banner">
        <div class="container">
            <h1 id="pageTitle">FAQ</h1>
            <p style="color: var(--text-muted); margin-top: 5px;">Looking for something in particular?</p>
            <div class="search-row">
                <input type="text" placeholder="Search our documentation...">
                <button>Search</button>
            </div>
        </div>
    </div>

    <div id="faq-tab" class="container tab-content active">
        <h2 style="margin-top: 50px; font-size: 24px;">Top questions</h2>
        <div class="faq-grid">
            <div class="faq-col">
                <h3>Tests</h3>
                <ul>
                    <li><a href="#">Can I Randomize Test Questions?</a></li>
                    <li><a href="#">How to Edit Test settings?</a></li>
                    <li><a href="#">How to duplicate tests?</a></li>
                </ul>
            </div>
            <div class="faq-col">
                <h3>Results</h3>
                <ul>
                    <li><a href="#">What results can I see?</a></li>
                    <li><a href="#">Can I create custom certificates?</a></li>
                    <li><a href="#">Can I create tests in multiple languages?</a></li>
                </ul>
            </div>
            <div class="faq-col">
                <h3>Account</h3>
                <ul>
                    <li><a href="#">How do Credits work?</a></li>
                    <li><a href="#">Can I add multiple administrators?</a></li>
                    <li><a href="#">Can I hand my account to over someone?</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div id="manual-tab" class="container tab-content manual-section">
        <div class="manual-header">
            <h2 style="font-size: 24px; font-weight: 700; margin: 0;">CheckNow system at a glance</h2>
            <p class="manual-download">
                <a href="#"><i class="fa-solid fa-circle-down"></i> Download the CheckNow Essentials Guide</a>
            </p>
        </div>

        <div class="system-diagram-container">
            <img src="./images/faquser.png" alt="CheckNow System Flow Diagram">
        </div>
    </div>
        <?php include 'footer.php'; ?>


    <script>
        function switchTab(event, tab) {
            // Tab Toggle Logic
            document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('active'));
            event.currentTarget.classList.add('active');

            // Section Visibility
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.getElementById(tab + '-tab').classList.add('active');

            // Dynamic Title Update
            document.getElementById('pageTitle').innerText = (tab === 'faq') ? 'FAQ' : 'User manual';
        }
    </script>

</body>

</html>