<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $topic = $_POST['topic'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("
        INSERT INTO contact_messages
        (name, email, username, role, topic, subject, message)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssssss", $name, $email, $username, $role, $topic, $subject, $message);

    if ($stmt->execute()) {
        header("Location: contact.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>