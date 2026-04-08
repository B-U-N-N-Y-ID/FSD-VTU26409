<?php
// DO NOT add session_start() here. It is already handled securely inside config.php!
require_once '../config.php'; 

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'creator') {
    echo "Unauthorized"; 
    exit();
}

$creator_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['title'])) {
    echo "Invalid data received"; 
    exit();
}

// 1. Prepare Meta Data
$title = $conn->real_escape_string($data['title']);
$desc = $conn->real_escape_string($data['desc']);
$duration = (int)$data['duration'];
$total = (int)$data['total'];
$pass = (int)$data['pass'];
$category = $conn->real_escape_string($data['category']);
$status = $conn->real_escape_string($data['status']);

// 2. Insert into `tests`
$sql_test = "INSERT INTO tests (creator_id, test_name, description, duration, total_marks, pass_marks, category, status) 
             VALUES ('$creator_id', '$title', '$desc', '$duration', '$total', '$pass', '$category', '$status')";

if ($conn->query($sql_test) === TRUE) {
    $test_id = $conn->insert_id; 

    // 3. Loop and Insert Questions
    foreach ($data['questions'] as $q) {
        $q_text = $conn->real_escape_string($q['text']);
        if(empty($q_text)) continue;

        $opt_a = $conn->real_escape_string($q['a']);
        $opt_b = $conn->real_escape_string($q['b']);
        $opt_c = $conn->real_escape_string($q['c']);
        $opt_d = $conn->real_escape_string($q['d']);
        $correct = $conn->real_escape_string($q['correct']);

        // Save to `questions` table
        $sql_q = "INSERT INTO questions (test_id, question, option_a, option_b, option_c, option_d, correct_option) 
                  VALUES ('$test_id', '$q_text', '$opt_a', '$opt_b', '$opt_c', '$opt_d', '$correct')";
        $conn->query($sql_q);

        // Save to `question_bank` if the user left the checkbox checked
        if (isset($q['saveToBank']) && $q['saveToBank'] == true) {
            $sql_bank = "INSERT INTO question_bank (creator_id, category, question, option_a, option_b, option_c, option_d, correct_option) 
                         VALUES ('$creator_id', '$category', '$q_text', '$opt_a', '$opt_b', '$opt_c', '$opt_d', '$correct')";
            $conn->query($sql_bank);
        }
    }
    
    // JS reads this exact string to trigger the success message
    echo "success"; 
} else {
    echo "Database Error: " . $conn->error;
}
?>