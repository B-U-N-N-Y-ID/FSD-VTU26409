<?php
include "config.php";

$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $department = $_POST['department'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO students (name, email, dob, department, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $dob, $department, $phone);

    if ($stmt->execute()) {
        $success = "Student Registered Successfully!";
    } else {
        $success = "Error inserting data!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Register Student</title>

<style>
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: sans-serif;
    background-color: #f4f4f9;
}

section.details {
    display: flex;
    flex-direction: column;
    gap: 15px;
    border-radius: 8px;
    padding: 25px;
    background: white;
    box-shadow: 0 5px 15px rgba(105, 40, 236, 0.3);
    width: 320px;
}

label {
    display: flex;
    flex-direction: column;
    font-size: 14px;
    font-weight: bold;
    gap: 5px;
}

input {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    margin-top: 10px;
    cursor: pointer;
    padding: 10px;
    background: #6928ec;
    color: white;
    border: none;
    border-radius: 4px;
}

button:hover {
    background-color: #4e21a8;
}

.success {
    color: green;
    font-weight: bold;
    margin-bottom: 10px;
}
</style>
</head>

<body>

<form method="POST">
<section class="details">

<?php if($success != "") { ?>
    <div class="success"><?= $success ?></div>
<?php } ?>

    <label>Name
        <input type="text" name="name" required>
    </label>

    <label>Email
        <input type="email" name="email" required>
    </label>

    <label>Date of Birth
        <input type="date" name="dob" required>
    </label>

    <label>Department
        <input type="text" name="department" required>
    </label>

    <label>Phone
        <input type="tel" name="phone" required>
    </label>

    <button type="submit">Register</button>

</section>
</form>

</body>
</html>
