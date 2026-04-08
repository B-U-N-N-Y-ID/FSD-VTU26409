<?php
require_once 'config.php';

$toastScript = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_taker'])) {

    $fname = $conn->real_escape_string($_POST['first_name']);
    $lname = $conn->real_escape_string($_POST['last_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'taker';

    /* CHECK DUPLICATE */
    $check_sql = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $check_res = $conn->query($check_sql);

    if ($check_res->num_rows > 0) {

        $toastScript = "showGlobalToast('Error', 'Username or Email already exists.', 'error');";

    } else {

        $sql = "INSERT INTO users 
        (first_name,last_name,username,email,password,role)
        VALUES
        ('$fname','$lname','$username','$email','$password','$role')";

        if ($conn->query($sql) === TRUE) {

            /* AUTO LOGIN */

            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['first_name'] = $fname;
            $_SESSION['last_name'] = $lname;
            $_SESSION['role'] = $role;

            $toastScript = "
                showGlobalToast('Success!', 'Registration successful!', 'success');
                setTimeout(function(){
                    window.location.href='index.php';
                },1500);
            ";

        } else {

            $toastScript = "showGlobalToast('Error', 'Something went wrong.', 'error')";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Take Exam</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./css/navhead.css">

</head>
<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    min-height: 100vh;
}

/* wrapper fixes navbar conflict */
.page-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 140px);
    padding: 40px 20px;
}

.register-container {
    background: white;
    width: 100%;
    max-width: 900px;
    padding: 50px 60px;
    border-radius: 18px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.08);
}

.register-container h2 {
    font-size: 28px;
    margin-bottom: 40px;
    color: #0f172a;
    font-weight: 700;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px 40px;
}

.full-width {
    grid-column: 1 / -1;
}

label {
    font-size: 15px;
    font-weight: 600;
    color: #334155;
    display: block;
    margin-bottom: 8px;
}

.small-text {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 8px;
    line-height: 1.4;
}

input {
    width: 100%;
    padding: 14px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    background: #f8fafc;
    transition: 0.3s;
}

input:focus {
    background: #ffffff;
    border-color: #ef4444;
    outline: none;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
}

.register-btn {
    margin-top: 35px;
    background: linear-gradient(135deg, #ef4444, #f97316);
    color: white;
    padding: 14px 35px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
}

.register-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(239, 68, 68, 0.4);
}

/* ================= MODERN TOAST NOTIFICATION CSS ================= */
.toast-container {
    position: fixed;
    top: 30px;
    right: 30px;
    z-index: 10000;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.modern-toast {
    background: white;
    min-width: 300px;
    padding: 16px 24px;
    border-radius: 12px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    transform: translateX(150%);
    opacity: 0;
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.modern-toast.show {
    transform: translateX(0);
    opacity: 1;
}

.modern-toast.success { border-left: 6px solid #22c55e; }
.modern-toast.error { border-left: 6px solid #ef4444; }

.toast-icon { font-size: 24px; }
.modern-toast.success .toast-icon { color: #22c55e; }
.modern-toast.error .toast-icon { color: #ef4444; }

.toast-content {
    display: flex;
    flex-direction: column;
}

.toast-title {
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
}

.toast-desc {
    font-size: 13px;
    color: #64748b;
}

@media(max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    .register-container {
        padding: 35px 25px;
    }
}
</style>
<body>

<?php include 'header.php'; ?>

<div class="page-wrapper">
<div class="register-container">

<h2>Register to Take Exams</h2>

<form method="POST">

<div class="form-grid">

<div>
<label>First name *</label>
<input type="text" name="first_name" required>
</div>

<div>
<label>Last name *</label>
<input type="text" name="last_name" required>
</div>

<div>
<label>Username *</label>
<input type="text" name="username" required>
</div>

<div>
<label>Password *</label>
<input type="password" name="password" required>
</div>

<div class="full-width">
<label>Email address *</label>
<input type="email" name="email" required>
</div>

</div>

<button type="submit" name="register_taker" class="register-btn">
Register
</button>

</form>

</div>
</div>

<?php if($toastScript!=''): ?>

<script>
document.addEventListener("DOMContentLoaded",function(){

<?php echo $toastScript; ?>

});
</script>

<?php endif; ?>

</body>
</html>