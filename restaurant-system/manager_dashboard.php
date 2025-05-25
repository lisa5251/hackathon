<?php
session_start();
if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"] || $_SESSION["role"] !== "manager" || $_SESSION["username"] !== "Lisa Suliqi") {
    header("Location: login.php");
    exit();
}

// Load users from MySQL
$users = [];
$mysqli = new mysqli("localhost", "root", "", "hackathon_db");
if (!$mysqli->connect_errno) {
    $result = $mysqli->query("SELECT username, role FROM users");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = ["username" => $row["username"], "role" => $row["role"]];
        }
        $result->free();
    }
    $mysqli->close();
}

// Load orders from MySQL
$orders = [];
$mysqli = new mysqli("localhost", "root", "", "hackathon_db");
if (!$mysqli->connect_errno) {
    $result = $mysqli->query("SELECT * FROM orders ORDER BY time DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        $result->free();
    }
    $mysqli->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Dashboard</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <style>
        body { background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%); font-family: 'Montserrat', Arial, sans-serif; margin: 0; min-height: 100vh; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 2.5rem 2rem 2rem 2rem; border-radius: 22px; box-shadow: 0 8px 32px rgba(44,62,80,0.12); }
        h1 { color: #273c75; text-align: center; margin-bottom: 1.5rem; font-weight: 700; letter-spacing: 1px; }
        h2 { color: #4078c0; margin-top: 2.5rem; margin-bottom: 1rem; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 0.3rem; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        th, td { padding: 0.7rem 1rem; border-bottom: 1px solid #e1e1e1; text-align: left; }
        th { background: #f1f2f6; color: #273c75; font-weight: 700; }
        tr:last-child td { border-bottom: none; }
        .role-badge { display: inline-block; padding: 0.2em 0.7em; border-radius: 8px; font-size: 0.95em; font-weight: 600; }
        .role-waiter { background: #b8e994; color: #218c5a; }
        .role-chef { background: #ffeaa7; color: #b26500; }
        .order-status { font-weight: bold; text-transform: capitalize; }
        .status-available { color: #0097e6; }
        .status-taken { color: #fbc531; }
        .status-ready { color: #44bd32; }
        .status-served { color: #636e72; }
        .btn { display: inline-block; padding: 0.5em 1.2em; background: #4078c0; color: #fff; border: none; border-radius: 8px; font-size: 1em; font-weight: bold; cursor: pointer; text-decoration: none; transition: background 0.2s; margin-top: 1.2rem; }
        .btn:hover { background: #273c75; }
        .logout-link { float: right; color: #e84118; text-decoration: none; font-weight: bold; }
        .logout-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout-link">Logout</a>
        <h1>Manager Dashboard</h1>
        <a href="register_worker.php" class="btn">Register New Worker</a>
        <h2>Registered Workers</h2>
        <table>
            <tr><th>Username</th><th>Role</th></tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user["username"]); ?></td>
                    <td><span class="role-badge role-<?php echo htmlspecialchars($user["role"]); ?>"><?php echo ucfirst($user["role"]); ?></span></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <h2>All Orders</h2>
        <table>
            <tr><th>Table</th><th>Details</th><th>Status</th><th>Waiter</th><th>Chef</th><th>Time</th></tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order["table"] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($order["details"] ?? ''); ?></td>
                    <td class="order-status status-<?php echo htmlspecialchars($order["status"] ?? ''); ?>"><?php echo ucfirst($order["status"] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($order["waiter"] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($order["chef"] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($order["time"] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
