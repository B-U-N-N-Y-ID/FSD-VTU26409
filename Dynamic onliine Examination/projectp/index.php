<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Engine</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="./css/navhead.css">
</head>

<body>
<?php include 'header.php'; ?>

    <section class="hero">

        <div class="hero-container">

            <div class="hero-text">
                <h1>
                    Smart Online Testing for <br>
                    Education & Organizations
                </h1>

                <p>
                    Examination Portal is a secure and dynamic web-based system that simplifies the process of conducting online exams. It allows easy creation of customizable tests with automated evaluation, instant results, and performance analysis. With features like question randomization and digital reports or certificates, it reduces manual work and ensures a smooth, efficient examination experience.
                </p>

                <a href="registerfree.php" class="hero-btn">Try it free</a>
            </div>

            <div class="hero-image">
                <img src="./images/heroimage.jpg" alt="CHECKNOW Dashboard">
            </div>

        </div>

    </section>

    <section class="student-flow-section">
        <h1>How Checknow works</h1>

        <div class="student-flow-container">

            <div class="student-flow-step student-step-create">
                <div class="student-image-box">
                    <img src="./images/create.png" alt="Create Exam">
                </div>
                <h3>Create Exam</h3>
                <ul>
                    <li>Add questions easily.</li>
                    <li>Reuse question banks.</li>
                    <li>Randomize questions & answers.</li>
                </ul>
                <a href="#" class="student-link">Creating exams →</a>
            </div>

            <div class="student-flow-step student-step-setup">
                <div class="student-image-box">
                    <img src="./images/setup.png" alt="Setup Exam">
                </div>
                <h3>Setup Exam</h3>
                <ul>
                    <li>Private / public access.</li>
                    <li>Allow answer changes.</li>
                    <li>Set timers & attempts.</li>
                </ul>
                <a href="#" class="student-link">Exam settings →</a>
            </div>

            <div class="student-flow-step student-step-give">
                <div class="student-image-box">
                    <img src="./images/giveexam.png" alt="Give Exam">
                </div>
                <h3>Give Exam</h3>
                <ul>
                    <li>Works on mobile & desktop.</li>
                    <li>Instant feedback.</li>
                    <li>AI Proctoring enabled.</li>
                </ul>
                <a href="#" class="student-link">Taking exams →</a>
            </div>

            <div class="student-flow-step student-step-analyze">
                <div class="student-image-box">
                    <img src="./images/analysis.png" alt="Analyze Results">
                </div>
                <h3>Analyze Results</h3>
                <ul>
                    <li>Instant grading.</li>
                    <li>Real-time analytics.</li>
                </ul>
                <a href="#" class="student-link">Analyzing results →</a>
            </div>

            <div class="student-flow-step student-step-cert">
                <div class="student-image-box">
                    <img src="./images/certificate.png" alt="Certification">
                </div>
                <h3>Certification</h3>
                <ul>
                    <li>Auto PDF certificate.</li>
                    <li>Set pass mark criteria.</li>
                </ul>
                <a href="#" class="student-link">Custom certificates →</a>
            </div>
        </div>
    </section>
<?php include 'footer.php'; ?>
    
</body>

</html>