<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'creator') {
    header("Location: ../index.php");
    exit();
}

$creator_id = $_SESSION['user_id'];

$questions = [];

$res = $conn->query("
SELECT * FROM question_bank
WHERE creator_id='$creator_id'
ORDER BY created_at DESC
");

if($res){
    while($row = $res->fetch_assoc()){
        $questions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Question Bank</title>

<link rel="stylesheet" href="../css/admin.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>

body{
background:#f8fafc;
font-family:'Poppins',sans-serif;
}

.page-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
}

.add-btn{
background:#3b82f6;
color:white;
border:none;
padding:10px 18px;
border-radius:8px;
cursor:pointer;
font-weight:600;
}

.question-card{
background:white;
padding:20px;
border-radius:14px;
margin-bottom:15px;
box-shadow:0 4px 10px rgba(0,0,0,0.05);
}

.question-card h4{
margin-bottom:10px;
}

.options{
margin-top:10px;
font-size:14px;
color:#475569;
}

.correct{
color:#10b981;
font-weight:600;
}

.actions{
margin-top:10px;
}

.actions a{
font-size:13px;
margin-right:10px;
color:#3b82f6;
text-decoration:none;
}

</style>

</head>

<body>

<div class="main-content">

<div class="page-header">
<h2>Question Bank</h2>

<a href="add_question.php">
<button class="add-btn">+ Add Question</button>
</a>

</div>

<?php if(empty($questions)): ?>

<p>No questions added yet.</p>

<?php else: ?>

<?php foreach($questions as $q): ?>

<div class="question-card">

<h4><?php echo htmlspecialchars($q['question']); ?></h4>

<div class="options">

A. <?php echo htmlspecialchars($q['option_a']); ?><br>
B. <?php echo htmlspecialchars($q['option_b']); ?><br>
C. <?php echo htmlspecialchars($q['option_c']); ?><br>
D. <?php echo htmlspecialchars($q['option_d']); ?><br>

</div>

<p class="correct">
Correct Answer: <?php echo $q['correct_option']; ?>
</p>

<div class="actions">
<a href="edit_question.php?id=<?php echo $q['id']; ?>">Edit</a>
<a href="delete_question.php?id=<?php echo $q['id']; ?>">Delete</a>
</div>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>

</body>
</html>