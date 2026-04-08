<?php
require_once '../config.php';
session_start();

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['ids'])){
    echo "error";
    exit;
}

foreach($data['ids'] as $id){
    $id = intval($id);
    $conn->query("UPDATE tests SET status='Active' WHERE id='$id'");
}

echo "success";