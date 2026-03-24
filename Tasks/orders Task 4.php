<?php
include "config.php";

// JOIN query (Order History)
$query = "
SELECT customers.name AS customer_name, 
       products.name AS product_name, 
       products.price, 
       orders.quantity, 
       (products.price * orders.quantity) AS total,
       orders.order_date
FROM orders
JOIN customers ON orders.customer_id = customers.id
JOIN products ON orders.product_id = products.id
ORDER BY orders.order_date DESC
";

$result = $conn->query($query);

// Highest value order (Subquery)
$highestQuery = "
SELECT MAX(products.price * orders.quantity) AS highest_order
FROM orders
JOIN products ON orders.product_id = products.id
";
$highestResult = $conn->query($highestQuery)->fetch_assoc();

// Most active customer (Subquery)
$activeQuery = "
SELECT name FROM customers WHERE id = (
    SELECT customer_id 
    FROM orders 
    GROUP BY customer_id 
    ORDER BY COUNT(*) DESC 
    LIMIT 1
)";
$activeResult = $conn->query($activeQuery)->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Order Management</title>

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

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
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

.summary {
    margin-top: 20px;
    padding: 15px;
    background: white;
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}
</style>
</head>

<body>

<h2>Customer Order History</h2>

<table>
<tr>
    <th>Customer</th>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Total</th>
    <th>Date</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['customer_name'] ?></td>
    <td><?= $row['product_name'] ?></td>
    <td><?= $row['price'] ?></td>
    <td><?= $row['quantity'] ?></td>
    <td><?= $row['total'] ?></td>
    <td><?= $row['order_date'] ?></td>
</tr>
<?php } ?>

</table>

<div class="summary">
    <h3>Summary</h3>
    <p><strong>Highest Order Value:</strong> ₹<?= $highestResult['highest_order'] ?></p>
    <p><strong>Most Active Customer:</strong> <?= $activeResult['name'] ?></p>
</div>

</body>
</html>