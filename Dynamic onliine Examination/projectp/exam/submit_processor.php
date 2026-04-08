<?php
require_once '../config.php';

$data=json_decode(file_get_contents("php://input"),true);

$test_id=$data['test_id'];
$answers=$data['answers'];

$attempt=$_SESSION['attempt_id'];

$q=$conn->query("SELECT correct_option FROM questions WHERE test_id='$test_id'");
$db=$q->fetch_all(MYSQLI_ASSOC);

$correct=0;

foreach($db as $i=>$r){

if(isset($answers[$i]) &&
strtoupper($answers[$i])==strtoupper($r['correct_option'])){

$correct++;

}

}

$total=count($db);

$score=($correct/$total)*100;

$pass=$score>=40?1:0;

$stmt=$conn->prepare("
UPDATE test_attempts
SET score=?,passed=?,status='Completed',completed_at=NOW()
WHERE id=?
");

$stmt->bind_param("dii",$score,$pass,$attempt);
$stmt->execute();

echo json_encode(["status"=>"success"]);