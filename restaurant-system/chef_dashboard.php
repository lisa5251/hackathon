<?php
session_start();
// For demo, no chef login. In production, add authentication.
$mysqli = new mysqli("localhost", "root", "", "hackathon_db");
$orders = [];
if (!$mysqli->connect_errno) {
    $result = $mysqli->query("SELECT * FROM orders ORDER BY time DESC");
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $result->free();
}
// Handle marking as ready
if (isset($_POST['ready_order'])) {
    $ready_id = intval($_POST['ready_order']);
    $stmt = $mysqli->prepare("UPDATE orders SET status='ready', chef='Chef' WHERE id=? AND status='taken'");
    $stmt->bind_param("i", $ready_id);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
    header("Location: chef_dashboard.php");
    exit();
}
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chef Dashboard</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <style>
        body { background: linear-gradient(120deg, #f6d365 0%, #fda085 100%); font-family: 'Montserrat', Arial, sans-serif; margin: 0; min-height: 100vh; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; padding: 2.5rem 2rem 2rem 2rem; border-radius: 22px; box-shadow: 0 8px 32px rgba(44,62,80,0.18); }
        h1 { color: #b26500; text-align: center; margin-bottom: 1.5rem; font-weight: 700; letter-spacing: 1px; }
        h2 { color: #e17055; margin-top: 2.5rem; margin-bottom: 1rem; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 0.3rem; }
        .order { background: #f1f2f6; padding: 1.2rem 1.4rem; border-radius: 14px; margin-bottom: 1.2rem; box-shadow: 0 2px 8px rgba(44,62,80,0.06); position: relative; border-left: 6px solid #e17055; }
        .order-title { font-weight: bold; color: #e17055; margin-bottom: 0.3rem; font-size: 1.1em; }
        .order-time { font-size: 0.95em; color: #888; margin-bottom: 0.5rem; }
        .order-details { margin-bottom: 0.7rem; color: #222f3e; }
        .order-waiter { font-size: 0.95em; color: #888; }
        .center { text-align: center; }
        form.inline { display: inline; }
        button { margin-top: 0.5rem; padding: 0.6rem 1.2rem; background: linear-gradient(90deg, #e17055 0%, #f6d365 100%); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: background 0.2s, transform 0.1s; box-shadow: 0 2px 8px rgba(44,62,80,0.08); }
        button:hover { background: linear-gradient(90deg, #f6d365 0%, #e17055 100%); transform: translateY(-2px) scale(1.03); }
    </style>
</head>
<body>
    <div class="container">
        <h1>👨‍🍳 Chef Dashboard</h1>
        <h2>Orders to Prepare</h2>
        <?php
        $has_taken = false;
        foreach ($orders as $i => $order) {
            if ($order['status'] === 'taken') {
                $has_taken = true;
                echo '<div class="order">';
                echo '<div class="order-title">Table ' . htmlspecialchars($order['table_number'] ?? '-') . '</div>';
                echo '<div class="order-time">' . htmlspecialchars($order['time']) . '</div>';
                echo '<div class="order-details">' . nl2br(htmlspecialchars($order['details'])) . '</div>';
                echo '<div class="order-waiter">Waiter: <b>' . htmlspecialchars($order['waiter']) . '</b></div>';
                echo '<form method="post" class="inline"><input type="hidden" name="ready_order" value="' . $order['id'] . '"><button type="submit">Mark as Ready</button></form>';
                echo '</div>';
            }
        }
        if (!$has_taken) echo '<p class="center" style="color:#888;">No orders to prepare.</p>';
        ?>
        <h2>Ready Orders</h2>
        <?php
        $has_ready = false;
        foreach ($orders as $order) {
            if ($order['status'] === 'ready') {
                $has_ready = true;
                echo '<div class="order">';
                echo '<div class="order-title">Table ' . htmlspecialchars($order['table_number'] ?? '-') . '</div>';
                echo '<div class="order-time">' . htmlspecialchars($order['time']) . '</div>';
                echo '<div class="order-details">' . nl2br(htmlspecialchars($order['details'])) . '</div>';
                echo '<div class="order-waiter">Waiter: <b>' . htmlspecialchars($order['waiter']) . '</b></div>';
                echo '</div>';
            }
        }
        if (!$has_ready) echo '<p class="center" style="color:#888;">No ready orders.</p>';
        ?>
    </div>
</body>
</html>
