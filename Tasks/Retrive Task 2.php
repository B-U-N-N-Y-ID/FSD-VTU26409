<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include "config.php";

// Get filter & sort values
$department = isset($_GET['department']) ? $_GET['department'] : "";
$sort = isset($_GET['sort']) ? $_GET['sort'] : "";

// Base query
$query = "SELECT * FROM students WHERE 1";

// Filtering
if ($department != "") {
    $query .= " AND department='$department'";
}

// Sorting
if ($sort == "name") {
    $query .= " ORDER BY name ASC";
} elseif ($sort == "dob") {
    $query .= " ORDER BY dob ASC";
} else {
    $query .= " ORDER BY id DESC";
}

$result = $conn->query($query);

// Count per department
$countQuery = "SELECT department, COUNT(*) as total FROM students GROUP BY department";
$countResult = $conn->query($countQuery);
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>

<style>
body {
    font-family: sans-serif;
    background: #f4f4f9;
    padding: 40px;
}

h2 {
    text-align: center;
    color: #6928ec;
}

.controls {
    margin-bottom: 20px;
    text-align: center;
}

select, button {
    padding: 8px;
    margin: 5px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    margin-top: 20px;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: center;
}

th {
    background: #6928ec;
    color: white;
}

.count-box {
    margin-top: 30px;
    background: white;
    padding: 15px;
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}
</style>
</head>

<body>

<h2>Student Dashboard</h2>


<form method="GET" class="controls">
    

    <select name="department">
        <option value="">All Departments</option>
        <option value="CSE">CSE</option>
        <option value="ECE">ECE</option>
        <option value="EEE">EEE</option>
        <option value="MECH">MECH</option>
    </select>

    <!-- Sort -->
    <select name="sort">
        <option value="">Sort By</option>
        <option value="name">Name</option>
        <option value="dob">DOB</option>
    </select>

    <button type="submit">Apply</button>

</form>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>DOB</th>
    <th>Department</th>
    <th>Phone</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['id']; ?></td>
    <td><?= $row['name']; ?></td>
    <td><?= $row['email']; ?></td>
    <td><?= $row['dob']; ?></td>
    <td><?= $row['department']; ?></td>
    <td><?= $row['phone']; ?></td>
</tr>
<?php } ?>

</table>

<div class="count-box">
    <h3>Students per Department</h3>

    <table>
        <tr>
            <th>Department</th>
            <th>Total Students</th>
        </tr>

        <?php while($row = $countResult->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['department']; ?></td>
            <td><?= $row['total']; ?></td>
        </tr>
        <?php } ?>

    </table>
</div>

</body>
</html>