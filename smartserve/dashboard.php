<?php
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome, <?php echo $_SESSION['waiter']; ?>!</h1>
        <a href="logout.php" class="btn btn-danger">Logout</a>
        <a href="submit_order.php" class="btn btn-primary">Submit Order</a>
        <a href="view_orders.php" class="btn btn-success">View Orders</a>
    </div>
</body>
</html>
