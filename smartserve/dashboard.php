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
    <title>Document</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
<h2 class="mb-4">Welcome, <?= $_SESSION['waiter'] ?> 👋</h2>

<form action="submit_order.php" method="POST" class="card p-4 shadow-sm">
    <div class="mb-3">
        <label class="form-label">Select Table:</label>
        <select name="table_number" class="form-select" required>
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <option value="<?= $i ?>">Table <?= $i ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Order Details:</label>
        <textarea name="order_details" class="form-control" rows="4" placeholder="e.g. 2x Pasta, 1x Coke" required></textarea>
    </div>

    <button type="submit" class="btn btn-success">Submit Order</button>
</form>

    
<a class="btn btn-link mt-3" href="view_orders.php">➡️ View Active Orders</a>
</div>
</body>
</html>