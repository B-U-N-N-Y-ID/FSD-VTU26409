<?php
require_once 'config.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$toastScript = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_creator'])) {

    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $institution = $_POST['institution'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $role = "creator";

    /* CHECK DUPLICATE USER */
    $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $check->bind_param("ss",$username,$email);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows > 0){

        $toastScript = "showGlobalToast('Error','Username or Email already exists','error');";

    }else{

        /* INSERT USER */

        $stmt = $conn->prepare("INSERT INTO users 
        (first_name,last_name,institution,username,email,password,role)
        VALUES (?,?,?,?,?,?,?)");

        $stmt->bind_param(
            "sssssss",
            $fname,
            $lname,
            $institution,
            $username,
            $email,
            $password,
            $role
        );

        if($stmt->execute()){

            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['first_name'] = $fname;
            $_SESSION['last_name'] = $lname;
            $_SESSION['role'] = $role;

            $toastScript = "
            showGlobalToast('Success!','Account created! Redirecting to dashboard...','success');
            setTimeout(function(){
                window.location.href='./Admin/navbar.php';
            },1500);
            ";

        }else{

            $error = $stmt->error;
            $toastScript = "showGlobalToast('Database Error','$error','error')";

        }

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>Register - Create Exam</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link rel="stylesheet" href="./css/navhead.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:linear-gradient(135deg,#f1f5f9,#e2e8f0);
min-height:100vh;
}

.page-wrapper{
display:flex;
justify-content:center;
align-items:center;
min-height:calc(100vh - 80px);
padding:40px 20px;
}

.register-container{
background:white;
width:100%;
max-width:950px;
padding:50px 60px;
border-radius:18px;
box-shadow:0 25px 60px rgba(0,0,0,0.08);
}

.register-container h2{
font-size:28px;
margin-bottom:40px;
color:#0f172a;
}

.form-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:35px 40px;
}

.full-width{
grid-column:1/-1;
}

label{
font-size:15px;
font-weight:600;
color:#334155;
margin-bottom:8px;
display:block;
}

input{
width:100%;
padding:14px;
border-radius:10px;
border:1px solid #d1d5db;
font-size:14px;
}

input:focus{
border-color:#ef4444;
outline:none;
box-shadow:0 0 0 3px rgba(239,68,68,0.15);
}

.register-btn{
margin-top:35px;
background:linear-gradient(135deg,#ef4444,#f97316);
color:white;
padding:14px 30px;
border:none;
border-radius:10px;
font-size:15px;
font-weight:600;
cursor:pointer;
transition:0.3s;
}

.register-btn:hover{
transform:translateY(-3px);
box-shadow:0 15px 35px rgba(239,68,68,0.4);
}

@media(max-width:768px){
.form-grid{
grid-template-columns:1fr;
}

.register-container{
padding:35px 25px;
}
}

/* Toast */

.toast{
position:fixed;
top:30px;
right:30px;
background:white;
padding:16px 24px;
border-radius:10px;
box-shadow:0 15px 40px rgba(0,0,0,0.15);
display:flex;
align-items:center;
gap:10px;
z-index:9999;
}

.toast.success{
border-left:5px solid #22c55e;
}

.toast.error{
border-left:5px solid #ef4444;
}

</style>

</head>

<body>

<?php include 'header.php'; ?>

<div class="page-wrapper">
<div class="register-container">

<h2>Register to Create Exams</h2>

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

<div class="full-width">
<label>College / Institution Name *</label>
<input type="text" name="institution" required>
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

<button type="submit" name="register_creator" class="register-btn">
Create Account
</button>

</form>

</div>
</div>

<script>

function showGlobalToast(title,message,type){

const toast=document.createElement("div");

toast.className="toast "+type;

toast.innerHTML="<strong>"+title+"</strong> - "+message;

document.body.appendChild(toast);

setTimeout(()=>{
toast.remove();
},3500);

}

<?php if($toastScript!=''): ?>

document.addEventListener("DOMContentLoaded",function(){

<?php echo $toastScript; ?>

});

<?php endif; ?>

</script>

</body>
</html>