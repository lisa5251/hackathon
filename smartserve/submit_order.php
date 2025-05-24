<?php
session_start();
include 'config.php';

$table = $_POST['table_number'];
$order = htmlspecialchars($_POST['order_details']);
$waiter = $_SESSION['waiter'];

$stmt = $conn->prepare("INSERT INTO orders (waiter, table_number, order_details) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $waiter, $table, $order);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Order submitted successfully.";
} else {
    echo "Failed to submit order.";
}
?>
