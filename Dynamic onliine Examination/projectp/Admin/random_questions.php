<?php
// DO NOT add session_start() here. It is already handled securely inside config.php!
require_once '../config.php';

// 1. Security Check: Make sure the user is logged in and is a creator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'creator') {
    echo json_encode(['error' => 'Unauthorized access. Please log in.']);
    exit();
}

$creator_id = $_SESSION['user_id'];

// 2. Sanitize Input: Force the count to be a strict integer
$count = isset($_GET['count']) ? (int)$_GET['count'] : 1;
if ($count <= 0) {
    $count = 1;
}

// 3. Secure Query: Only fetch random questions from THIS specific creator's bank
$sql = "SELECT * FROM question_bank WHERE creator_id = '$creator_id' ORDER BY RAND() LIMIT $count";
$res = $conn->query($sql);

$data = [];

// Check if the query was successful
if ($res) {
    while($row = $res->fetch_assoc()){
        $data[] = $row;
    }
}

// 4. Return the data properly formatted as JSON without any PHP errors mixed in
header('Content-Type: application/json');
echo json_encode($data);
?>