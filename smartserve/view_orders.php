<?php
include 'config.php';
$orders = $conn->query("SELECT * FROM orders WHERE status = 'active' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>SmartServe – Active Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Active Orders</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Waiter</th>
                    <th>Table</th>
                    <th>Details</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['waiter'] ?></td>
                        <td><?= $row['table_number'] ?></td>
                        <td><?= $row['order_details'] ?></td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
