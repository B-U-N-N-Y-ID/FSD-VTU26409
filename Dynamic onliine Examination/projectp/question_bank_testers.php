<?php
require_once 'config.php';

/* SAFE TEST ID */
$test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : null;

if ($test_id) {

    /* Fetch questions */
    $questions = $conn->query("
        SELECT * FROM questions 
        WHERE test_id='$test_id'
    ");

    /* Fetch bank info */
    $test = $conn->query("
        SELECT test_name, category 
        FROM tests 
        WHERE id='$test_id' AND status='Active'
    ")->fetch_assoc();

    /* Safety redirect */
    if (!$test) {
        header("Location: question_bank_testers.php");
        exit();
    }

} else {

    /* Fetch only ACTIVE banks */
    $tests = $conn->query("
        SELECT t.id, t.test_name, t.category, COUNT(q.id) as total_questions
        FROM tests t
        LEFT JOIN questions q ON t.id = q.test_id
        WHERE t.status='Active'
        GROUP BY t.id
        ORDER BY t.test_name ASC
    ");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Premium Question Bank | Study Center</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

:root{
--primary:#3b82f6;
--primary-gradient:linear-gradient(135deg,#3b82f6 0%,#2563eb 100%);
--accent-red:#ff3b1f;
--bg:#f0f4f8;
--card-shadow:0 10px 25px -5px rgba(0,0,0,0.05),0 8px 10px -6px rgba(0,0,0,0.05);
--glass:rgba(255,255,255,0.8);
}

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:var(--bg);
color:#1e293b;
padding:40px 20px;
background-image:radial-gradient(#cbd5e1 0.5px,transparent 0.5px);
background-size:20px 20px;
}

.container{
max-width:1100px;
margin:auto;
}

/* HEADER */

.section-header{
margin-bottom:40px;
animation:fadeInDown 0.6s ease;
}

.section-header h2{
font-size:32px;
font-weight:800;
background:linear-gradient(to right,#0f172a,#334155);
-webkit-background-clip:text;
-webkit-text-fill-color:transparent;
}

/* BACK BUTTON */

.back-link{
display:inline-flex;
align-items:center;
gap:10px;
text-decoration:none;
color:#64748b;
font-weight:600;
padding:10px 20px;
background:white;
border-radius:12px;
margin-bottom:25px;
transition:0.3s;
box-shadow:var(--card-shadow);
}

.back-link:hover{
color:var(--primary);
transform:translateX(-5px);
}

/* BANK GRID */

.bank-grid{
display:grid;
grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
gap:25px;
}

.bank-card{
background:var(--glass);
backdrop-filter:blur(10px);
border:1px solid rgba(255,255,255,0.5);
padding:35px;
border-radius:24px;
transition:0.4s cubic-bezier(0.175,0.885,0.32,1.275);
position:relative;
display:flex;
flex-direction:column;
box-shadow:var(--card-shadow);
}

.bank-card:hover{
transform:translateY(-12px);
box-shadow:0 20px 30px -10px rgba(59,130,246,0.2);
border-color:var(--primary);
}

.category-badge{
background:#dbeafe;
color:var(--primary);
font-size:11px;
font-weight:800;
padding:6px 16px;
border-radius:100px;
text-transform:uppercase;
letter-spacing:1px;
margin-bottom:20px;
width:fit-content;
}

.bank-card h3{
font-size:22px;
margin-bottom:12px;
color:#0f172a;
}

.view-link{
margin-top:30px;
background:var(--primary-gradient);
color:white;
text-align:center;
padding:14px;
border-radius:16px;
text-decoration:none;
font-weight:700;
transition:0.3s;
box-shadow:0 4px 15px rgba(59,130,246,0.3);
}

.view-link:hover{
box-shadow:0 8px 20px rgba(59,130,246,0.4);
transform:scale(1.02);
}

/* QUESTIONS */

.question-item{
background:white;
padding:40px;
border-radius:28px;
margin-bottom:25px;
box-shadow:var(--card-shadow);
border-left:6px solid #e2e8f0;
transition:0.3s;
}

.question-item:hover{
border-left-color:var(--primary);
}

.question-text{
font-size:20px;
font-weight:700;
color:#0f172a;
margin-bottom:25px;
display:block;
}

.options-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:15px;
}

.option{
background:#f8fafc;
padding:16px 20px;
border-radius:14px;
font-size:15px;
border:1px solid #edf2f7;
display:flex;
align-items:center;
}

.option b{
color:var(--primary);
margin-right:10px;
}

.answer-key{
margin-top:25px;
padding:15px 25px;
background:#f0fdf4;
border-radius:12px;
display:inline-flex;
align-items:center;
gap:10px;
font-weight:700;
color:#16a34a;
}

@keyframes fadeInDown{
from{opacity:0;transform:translateY(-20px);}
to{opacity:1;transform:translateY(0);}
}

@media(max-width:768px){
.options-grid{grid-template-columns:1fr;}
}

</style>
</head>

<body>

<div class="container">

<?php if ($test_id) { ?>

<!-- BACK TO DASHBOARD QUESTION BANK -->

<a href="makeit.php?section=questionbank#questionbank" class="back-link">
<i class="fa-solid fa-arrow-left"></i> Back to Question Banks
</a>

<div class="section-header">
<h2><?php echo htmlspecialchars($test['test_name']); ?></h2>
<p style="color:#64748b;font-weight:500;">
Module: <?php echo htmlspecialchars($test['category']); ?>
</p>
</div>

<?php

if ($questions && $questions->num_rows > 0) {

$i=1;

while ($q = $questions->fetch_assoc()) {

?>

<div class="question-item">

<span class="question-text">
<span style="color:var(--primary);font-size:14px;display:block;margin-bottom:5px;">
Question <?php echo $i++; ?>
</span>

<?php echo htmlspecialchars($q['question']); ?>
</span>

<div class="options-grid">

<div class="option"><b>A</b> <?php echo htmlspecialchars($q['option_a']); ?></div>
<div class="option"><b>B</b> <?php echo htmlspecialchars($q['option_b']); ?></div>
<div class="option"><b>C</b> <?php echo htmlspecialchars($q['option_c']); ?></div>
<div class="option"><b>D</b> <?php echo htmlspecialchars($q['option_d']); ?></div>

</div>

<div class="answer-key">
<i class="fa-solid fa-circle-check"></i>
VERIFIED ANSWER: <?php echo strtoupper($q['correct_option']); ?>
</div>

</div>

<?php
}

} else {
echo "<div class='bank-card' style='text-align:center'>No questions currently published in this bank.</div>";
}

?>

<?php } else { ?>

<!-- BANK LIST -->

<div class="section-header">

<div style="display:flex;justify-content:space-between;align-items:center;">

<div>
<h2>Educational Library</h2>
<p style="color:#64748b;">Browse verified materials published by the administration.</p>
</div>

<a href="makeit.php?section=questionbank#questionbank"
style="background:white;padding:12px;border-radius:50%;box-shadow:var(--card-shadow);color:#0f172a;">
<i class="fa-solid fa-arrow-left"></i>
</a>

</div>

</div>

<div class="bank-grid">

<?php

if ($tests && $tests->num_rows > 0) {

while ($row = $tests->fetch_assoc()) {

?>

<div class="bank-card">

<div class="category-badge">
<?php echo htmlspecialchars($row['category']); ?>
</div>

<h3><?php echo htmlspecialchars($row['test_name']); ?></h3>

<p style="font-weight:500;">
<i class="fa-solid fa-layer-group" style="color:var(--primary);margin-right:8px;"></i>
<?php echo $row['total_questions']; ?> Practice Questions
</p>

<a href="question_bank_testers.php?test_id=<?php echo $row['id']; ?>" class="view-link">
Open Question Bank
</a>

</div>

<?php
}

} else {

echo "<div style='grid-column:1/-1;text-align:center;padding:100px 0;color:#94a3b8;'>
<i class='fa-solid fa-cloud-arrow-up' style='font-size:60px;margin-bottom:20px;opacity:0.5;'></i>
<p style='font-size:18px;'>No question banks have been published yet.</p>
</div>";

}

?>

</div>

<?php } ?>

</div>

</body>
</html>