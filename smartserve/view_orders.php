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
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">📋 Active Orders</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Table</th>
                <th>Order Details</th>
                <th>Waiter</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $orders->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['table_number']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['order_details'])) ?></td>
                <td><?= htmlspecialchars($row['waiter']) ?></td>
                <td><?= date('H:i', strtotime($row['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

<a href="dashboard.php" class="btn btn-secondary">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
