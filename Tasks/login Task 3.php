<?php
session_start();
include "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['user'] = $email;
        header("Location: /FSD/Tasks/retrive2.php");
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f4f4f9;
            font-family: sans-serif;
        }

        .form-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 300px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #6928ec;
            color: white;
            border: none;
        }

        .error {
            color: red;
            font-size: 14px;
        }
    </style>

    <script>
        function validateForm() {
            let email = document.forms["loginForm"]["email"].value;
            let password = document.forms["loginForm"]["password"].value;

            let errorMsg = "";

            if (email == "" || password == "") {
                errorMsg = "All fields are required!";
            }
            else if (!email.includes("@")) {
                errorMsg = "Enter valid email!";
            }

            if (errorMsg != "") {
                document.getElementById("error").innerText = errorMsg;
                return false;
            }

            return true;
        }
    </script>

</head>

<body>

    <div class="form-box">

        <h2>Login</h2>

        <form name="loginForm" method="POST" onsubmit="return validateForm()">

            <input type="email" name="email" placeholder="Enter Email">
            <input type="password" name="password" placeholder="Enter Password">

            <div id="error" class="error">
                <?= $error ?>
            </div>

            <button type="submit">Login</button>

        </form>

    </div>

</body>

</html>