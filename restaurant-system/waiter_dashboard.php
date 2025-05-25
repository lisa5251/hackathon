<?php
session_start();
if (!isset($_SESSION["waiter_name"])) {
    header("Location: waiter_register.php");
    exit();
}

$waiter_name = $_SESSION["waiter_name"];
$mysqli = new mysqli("localhost", "root", "", "hackathon_db");
$orders = [];
if (!$mysqli->connect_errno) {
    $result = $mysqli->query("SELECT * FROM orders ORDER BY time DESC");
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $result->free();
}

// Handle picking up an order
if (isset($_POST['pick_order'])) {
    $pick_id = intval($_POST['pick_order']);
    $stmt = $mysqli->prepare("UPDATE orders SET status='taken', waiter=? WHERE id=? AND status='available'");
    $stmt->bind_param("si", $waiter_name, $pick_id);
    $stmt->execute();
    $stmt->close();
    header("Location: waiter_dashboard.php");
    exit();
}
// Handle marking as served (only if ready)
if (isset($_POST['serve_order'])) {
    $serve_id = intval($_POST['serve_order']);
    $stmt = $mysqli->prepare("UPDATE orders SET status='served' WHERE id=? AND status='ready' AND waiter=?");
    $stmt->bind_param("is", $serve_id, $waiter_name);
    $stmt->execute();
    $stmt->close();
    header("Location: waiter_dashboard.php");
    exit();
}
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Waiter Dashboard</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <style>
        body { background: linear-gradient(120deg, #89f7fe 0%, #66a6ff 100%); font-family: 'Montserrat', Arial, sans-serif; margin: 0; min-height: 100vh; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; padding: 2.5rem 2rem 2rem 2rem; border-radius: 22px; box-shadow: 0 8px 32px rgba(44,62,80,0.18); }
        h1 { color: #222f3e; text-align: center; margin-bottom: 1.5rem; font-weight: 700; letter-spacing: 1px; }
        h2 { color: #0097e6; margin-top: 2.5rem; margin-bottom: 1rem; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 0.3rem; }
        .order { background: #f1f2f6; padding: 1.2rem 1.4rem; border-radius: 14px; margin-bottom: 1.2rem; box-shadow: 0 2px 8px rgba(44,62,80,0.06); position: relative; border-left: 6px solid #0097e6; }
        .order.taken { background: #fffbe6; border-left: 6px solid #fbc531; }
        .order.served { background: #dff9fb; border-left: 6px solid #44bd32; }
        .order-title { font-weight: bold; color: #0097e6; margin-bottom: 0.3rem; font-size: 1.1em; }
        .order-time { font-size: 0.95em; color: #888; margin-bottom: 0.5rem; }
        .order-details { margin-bottom: 0.7rem; color: #222f3e; }
        .order-waiter { font-size: 0.95em; color: #888; }
        .center { text-align: center; }
        form.inline { display: inline; }
        button { margin-top: 0.5rem; padding: 0.6rem 1.2rem; background: linear-gradient(90deg, #0097e6 0%, #00b894 100%); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: background 0.2s, transform 0.1s; box-shadow: 0 2px 8px rgba(44,62,80,0.08); }
        button:hover { background: linear-gradient(90deg, #00b894 0%, #0097e6 100%); transform: translateY(-2px) scale(1.03); }
    </style>
</head>
<body>
    <div class="container">
        <h1>👨‍🍳 Welcome, <?php echo htmlspecialchars($_SESSION["waiter_name"]); ?>!</h1>
        <h2>🟢 Available Orders</h2>
        <?php
        $has_available = false;
        foreach ($orders as $order) {
            if ($order['status'] === 'available') {
                $has_available = true;
                echo '<div class="order">';
                echo '<div class="order-title">Table ' . htmlspecialchars($order['table_number'] ?? '-') . '</div>';
                echo '<div class="order-time">' . htmlspecialchars($order['time']) . '</div>';
                echo '<div class="order-details">' . nl2br(htmlspecialchars($order['details'])) . '</div>';
                echo '<form method="post" class="inline"><input type="hidden" name="pick_order" value="' . $order['id'] . '"><button type="submit">Pick Order</button></form>';
                echo '</div>';
            }
        }
        if (!$has_available) echo '<p class="center" style="color:#888;">No available orders.</p>';
        ?>
        <h2>🟡 Taken Orders (Waiting for Chef)</h2>
        <?php
        $has_taken = false;
        foreach ($orders as $order) {
            if ($order['status'] === 'taken') {
                $has_taken = true;
                $taken_by = htmlspecialchars($order['waiter']);
                $mine = ($order['waiter'] === $_SESSION["waiter_name"]);
                echo '<div class="order taken">';
                echo '<div class="order-title">Table ' . htmlspecialchars($order['table_number'] ?? '-') . '</div>';
                echo '<div class="order-time">' . htmlspecialchars($order['time']) . '</div>';
                echo '<div class="order-details">' . nl2br(htmlspecialchars($order['details'])) . '</div>';
                echo '<div class="order-waiter">Taken by <b>' . $taken_by . '</b></div>';
                echo '</div>';
            }
        }
        if (!$has_taken) echo '<p class="center" style="color:#888;">No taken orders.</p>';
        ?>
        <h2>🟦 Ready to Serve</h2>
        <?php
        $has_ready = false;
        foreach ($orders as $order) {
            // Only show ready orders assigned to this waiter
            if ($order['status'] === 'ready' && $order['waiter'] === $_SESSION["waiter_name"]) {
                $has_ready = true;
                echo '<div class="order served">';
                echo '<div class="order-title">Table ' . htmlspecialchars($order['table_number'] ?? '-') . '</div>';
                echo '<div class="order-time">' . htmlspecialchars($order['time']) . '</div>';
                echo '<div class="order-details">' . nl2br(htmlspecialchars($order['details'])) . '</div>';
                echo '<form method="post" class="inline"><input type="hidden" name="serve_order" value="' . $order['id'] . '"><button type="submit">Mark as Served</button></form>';
                echo '</div>';
            }
        }
        if (!$has_ready) echo '<p class="center" style="color:#888;">No ready orders for you.</p>';
        ?>
    </div>
</body>
</html>
