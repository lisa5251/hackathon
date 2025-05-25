<?php
session_start();
// Only allow access if logged in
if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
    header("Location: login.php");
    exit();
}

// Only waiters see this page; others are redirected
if ($_SESSION["role"] !== "waiter") {
    if ($_SESSION["role"] === "manager") {
        header("Location: manager_dashboard.php");
        exit();
    } elseif ($_SESSION["role"] === "chef") {
        header("Location: chef_dashboard.php");
        exit();
    } else {
        header("Location: login.php");
        exit();
    }
}

$waiter = $_SESSION["username"];
$orders = [];
$month = date('m');
$year = date('Y');
$totalOrders = 0;
$deliveredOrders = 0;
$pendingOrders = 0;
$mysqli = new mysqli("localhost", "root", "", "hackathon_db");
if (!$mysqli->connect_errno) {
    $stmt = $mysqli->prepare("SELECT * FROM orders WHERE waiter = ?");
    $stmt->bind_param("s", $waiter);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($order = $result->fetch_assoc()) {
        $orderDate = isset($order['time']) ? $order['time'] : null;
        if ($orderDate) {
            $orderMonth = date('m', strtotime($orderDate));
            $orderYear = date('Y', strtotime($orderDate));
        } else {
            $orderMonth = $month;
            $orderYear = $year;
        }
        if ($orderMonth == $month && $orderYear == $year) {
            $orders[] = $order;
            $totalOrders++;
            if (isset($order['status']) && $order['status'] === 'ready') {
                $deliveredOrders++;
            } else {
                $pendingOrders++;
            }
        }
    }
    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Waiter Recap - <?php echo htmlspecialchars($waiter); ?></title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body { background: #f5f6fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .recap-container { max-width: 600px; margin: 40px auto; background: #fff; padding: 2.5rem 2rem; border-radius: 16px; box-shadow: 0 4px 24px rgba(44,62,80,0.12); }
    h1 { color: #273c75; text-align: center; margin-bottom: 1.5rem; }
    .summary { margin-bottom: 2rem; }
    .summary span { display: inline-block; margin-right: 1.5em; font-size: 1.1em; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 0.7rem 1rem; border-bottom: 1px solid #e1e1e1; text-align: left; }
    th { background: #f1f2f6; color: #273c75; font-weight: 700; }
    tr:last-child td { border-bottom: none; }
    .status-ready { color: #44bd32; font-weight: bold; }
    .status-pending { color: #e84118; font-weight: bold; }
  </style>
</head>
<body>
  <div class="recap-container">
    <h1>Monthly Recap for <?php echo htmlspecialchars($waiter); ?></h1>
    <div class="summary">
      <span><strong>Total Orders:</strong> <?php echo $totalOrders; ?></span>
      <span><strong>Delivered:</strong> <?php echo $deliveredOrders; ?></span>
      <span><strong>Pending:</strong> <?php echo $pendingOrders; ?></span>
    </div>
    <table>
      <tr>
        <th>Order #</th>
        <th>Table</th>
        <th>Status</th>
        <th>Date</th>
      </tr>
      <?php if (count($orders) === 0): ?>
        <tr><td colspan="4" style="text-align:center; color:#888;">No orders for this month.</td></tr>
      <?php else: ?>
        <?php foreach ($orders as $i => $order): ?>
          <tr>
            <td><?php echo $i+1; ?></td>
            <td><?php echo isset($order['table']) ? htmlspecialchars($order['table']) : '-'; ?></td>
            <td class="status-<?php echo (isset($order['status']) && $order['status'] === 'ready') ? 'ready' : 'pending'; ?>">
              <?php echo isset($order['status']) ? htmlspecialchars($order['status']) : 'pending'; ?>
            </td>
            <td><?php echo isset($order['date']) ? htmlspecialchars($order['date']) : 'N/A'; ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </table>
  </div>
</body>
</html>
