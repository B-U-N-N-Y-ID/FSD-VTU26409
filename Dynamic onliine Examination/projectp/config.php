<?php
$conn = new mysqli("localhost","root","","checknow_db");

if($conn->connect_error){
    die("Connection Failed: " . $conn->connect_error);
}

session_start();
?>