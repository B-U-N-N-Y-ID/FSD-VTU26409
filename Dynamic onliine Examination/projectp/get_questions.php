<?php
require 'config.php';

$test_id=$_GET['test_id'];

$q=$conn->query("SELECT * FROM questions WHERE test_id='$test_id'");

while($row=$q->fetch_assoc()){

echo "<div style='margin-top:20px;border-bottom:1px solid #eee;padding:10px'>";

echo "<b>".$row['question']."</b><br>";

echo "A. ".$row['option_a']."<br>";
echo "B. ".$row['option_b']."<br>";
echo "C. ".$row['option_c']."<br>";
echo "D. ".$row['option_d']."<br>";

echo "<span style='color:green;font-weight:600'>Correct: ".$row['correct_option']."</span>";

echo "</div>";

}