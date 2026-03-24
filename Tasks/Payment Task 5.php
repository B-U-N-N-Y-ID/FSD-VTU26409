<?php
include "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $amount = $_POST['amount'];

    // Account IDs
    $user_id = 1;
    $merchant_id = 2;

    // Start Transaction
    $conn->begin_transaction();

    try {

        // Get user balance
        $res = $conn->query("SELECT balance FROM accounts WHERE id=$user_id");
        $row = $res->fetch_assoc();
        $balance = $row['balance'];

        // Check sufficient balance
        if ($balance < $amount) {
            throw new Exception("Insufficient Balance!");
        }

        // Deduct from user
        $conn->query("UPDATE accounts SET balance = balance - $amount WHERE id=$user_id");

        // Add to merchant
        $conn->query("UPDATE accounts SET balance = balance + $amount WHERE id=$merchant_id");

        // Commit transaction
        $conn->commit();

        $message = "✅ Payment Successful!";

    } catch (Exception $e) {

        // Rollback if error
        $conn->rollback();

        $message = "❌ Payment Failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment Simulation</title>

<style>
body {
    font-family: sans-serif;
    background: #f4f4f9;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.box {
    background: white;
    padding: 25px;
    border-radius: 8px;
    width: 300px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

input, button {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
}

button {
    background: #6928ec;
    color: white;
    border: none;
}

.msg {
    font-weight: bold;
    text-align: center;
}
</style>
</head>

<body>

<div class="box">

<h2>Make Payment</h2>

<form method="POST">
    <input type="number" name="amount" placeholder="Enter Amount" required>
    <button type="submit">Pay</button>
</form>

<div class="msg">
    <?= $message ?>
</div>

</div>

</body>
</html>