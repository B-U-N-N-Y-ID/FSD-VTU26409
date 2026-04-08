<?php
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?login_required=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $test_id = $_POST['test_id'];
    $pass = $_POST['verify_pass'];
    $user = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $user);
    $stmt->execute();

    $db = $stmt->get_result()->fetch_assoc();

    if ($db && password_verify($pass, $db['password'])) {

        $stmt = $conn->prepare("
INSERT INTO test_attempts(test_id,student_id,status,started_at)
VALUES(?,?,'In Progress',NOW())
");

        $stmt->bind_param("ii", $test_id, $user);
        $stmt->execute();

        $_SESSION['current_test_id'] = $test_id;
        $_SESSION['attempt_id'] = $conn->insert_id;

        header("Location: exam_engine.php");
        exit();

    } else {

        echo "<script>alert('Wrong password');window.location='conduct_exam.php';</script>";

    }

}