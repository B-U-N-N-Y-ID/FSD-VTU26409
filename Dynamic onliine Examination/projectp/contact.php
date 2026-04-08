<?php
include "header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Us | CHECKNOW</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6f8;
            margin: 0;
            color: #333;
        }

        .container {
            width: 1100px;
            margin: auto;
        }

        /* TOP AREA - SEARCH FAQ */
        .contact-top {
            background: #e9ecef;
            padding: 50px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .contact-top h2 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .contact-top p {
            margin-top: 5px;
            color: #666;
            font-size: 15px;
        }

        .search-box {
            margin-top: 25px;
            display: flex;
            max-width: 600px;
        }

        .search-box input {
            flex: 1;
            padding: 14px 20px;
            border: 1px solid #ced4da;
            border-radius: 8px 0 0 8px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        .search-box input:focus {
            border-color: #ff4d2f;
        }

        .search-box button {
            background: #ff4d2f;
            border: none;
            color: white;
            padding: 0 30px;
            border-radius: 0 8px 8px 0;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            font-size: 13px;
        }

        .search-box button:hover {
            background: #e03e22;
        }

        /* MAIN LAYOUT */
        .contact-main {
            display: flex;
            gap: 50px;
            padding: 60px 0;
        }

        .left {
            flex: 2;
        }

        .right {
            flex: 1;
        }

        .left h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .support-line {
            color: #ff4d2f;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 30px;
            display: block;
        }

        /* FORM STYLING */
        .contact-form label {
            display: block;
            margin-top: 20px;
            font-weight: 600;
            font-size: 13px;
            color: #444;
        }

        .contact-form input,
        .contact-form textarea,
        .contact-form select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-top: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: 0.3s;
            background: #fff;
        }

        .contact-form input:focus,
        .contact-form textarea:focus,
        .contact-form select:focus {
            outline: none;
            border-color: #ff4d2f;
            box-shadow: 0 0 0 3px rgba(255, 77, 47, 0.1);
        }

        .radio-group {
            margin-top: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .radio-item input {
            accent-color: #ff4d2f;
            width: 18px;
            height: 18px;
        }

        .submit-btn {
            margin-top: 30px;
            background: #ff4d2f;
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(255, 77, 47, 0.2);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            background: #e03e22;
        }

        /* SIDEBAR CARDS */
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            border: 1px solid #eee;
        }

        .card h4 {
            margin-bottom: 20px;
            font-weight: 700;
            border-bottom: 2px solid #f5f6f8;
            padding-bottom: 10px;
        }

        .card ul {
            padding: 0;
            list-style: none;
        }

        .card li {
            margin-bottom: 12px;
            color: #ff4d2f;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .card li:hover {
            text-decoration: underline;
        }

        .status-box {
            background: #f0fdf4;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #dcfce7;
        }

        .status-box p {
            font-size: 13px;
            margin: 0;
            color: #15803d;
            font-weight: 600;
        }

        .footer-note {
            font-size: 12px;
            margin-top: 25px;
            color: #999;
            text-align: center;
        }

        .success-msg {
            background: #d4edda;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            color: #155724;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>

<body>

    <div class="contact-top">
        <div class="container">
            <h2>Contact us</h2>
            <p>Looking for something in particular?</p>
            <div class="search-box">
                <input type="text" placeholder="Search our technical documentation (FAQ)...">
                <button><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            </div>
        </div>
    </div>

    <div class="container contact-main">

        <div class="left">
            <h3>Get in touch</h3>
            <span class="support-line">7 Day support - Our team will get right back to you!</span>

            <?php if (isset($_GET['success'])): ?>
                <div class='success-msg'><i class="fa-solid fa-circle-check"></i> Message sent successfully! We'll contact
                    you shortly.</div>
            <?php endif; ?>

            <form class="contact-form" method="post" action="send_message.php">
                <label>Your name <span style="color:red">*</span></label>
                <input type="text" name="name" placeholder="Full name" required>

                <label>Your email address <span style="color:red">*</span></label>
                <input type="email" name="email" placeholder="registered-email@domain.com" required>

                <label>CheckNow Username (if registered)</label>
                <input type="text" name="username" placeholder="Optional">

                <label>I am...</label>
                <div class="radio-group">
                    <label class="radio-item"><input type="radio" name="role" value="Admin"> Administrator</label>
                    <label class="radio-item"><input type="radio" name="role" value="Assistant"> Assistant</label>
                    <label class="radio-item"><input type="radio" name="role" value="Taker"> Test taker</label>
                    <label class="radio-item"><input type="radio" name="role" value="None"> Not a customer</label>
                </div>

                <label>Topic</label>
                <select name="topic">
                    <option value="">-- Select one --</option>
                    <option value="Question Bank">Question Bank Help</option>
                    <option value="Proctoring">Proctoring & Security</option>
                    <option value="Certificates">Certificate Issues</option>
                    <option value="API">API & Integration</option>
                    <option value="Other">Other Inquiry</option>
                </select>

                <label>Subject <span style="color:red">*</span></label>
                <input type="text" name="subject" required>

                <label>Question / message <span style="color:red">*</span></label>
                <textarea name="message" required></textarea>

                <button class="submit-btn">Email us</button>

                <p class="footer-note">
                    Your data privacy is important to us. For full details see our <a href="#"
                        style="color:#ff4d2f">Privacy Policy</a>.
                </p>
            </form>
        </div>

        <div class="right">

            <div class="card">
                <h4>System FAQs</h4>
                <ul>
                    <li>How do I import Question Banks?</li>
                    <li>Can I set time limits per question?</li>
                    <li>How does automated grading work?</li>
                    <li>Where can I see Proctoring logs?</li>
                    <li>How do I issue bulk certificates?</li>
                    <li>Can I export results to Excel?</li>
                </ul>
                <p style="font-size:12px; margin-top:15px; font-weight:600; cursor:pointer; color:#ff4d2f">Full
                    documentation →</p>
            </div>

            <div class="card">
                <h4>Support Availability</h4>
                <p style="font-size:13px; color:#666; margin-bottom:15px;">Our technical proctors are monitoring the
                    system 24/7 for live assessment issues.</p>
                <div class="status-box">
                    <p><i class="fa-solid fa-clock-rotate-left"></i> Current Response Time</p>
                    <span style="font-size:12px; color:#15803d;">Under 30 Minutes</span>
                </div>
            </div>

            <div class="card">
                <h4>CheckNow Developer Hub</h4>
                <p style="font-size:13px; color:#666; margin-bottom:15px;">Integrate CheckNow with your LMS or custom
                    website using our Webhooks.</p>
                <div style="text-align: center; border: 1px dashed #ff4d2f; padding: 15px; border-radius: 8px;">
                    <i class="fa-solid fa-code" style="font-size:30px; color:#ff4d2f; margin-bottom:10px;"></i>
                    <button
                        style="display:block; width:100%; background:#333; color:white; border:none; padding:8px; border-radius:4px; font-size:12px; cursor:pointer;">
                        View API Docs
                    </button>
                </div>
            </div>

        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>