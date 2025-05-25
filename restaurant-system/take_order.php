<?php
session_start();
if (!isset($_SESSION["waiter_name"])) {
    header("Location: waiter_register.php");
    exit();
}

// Initialize orders arrays in session if not set
if (!isset($_SESSION["active_orders"])) $_SESSION["active_orders"] = [];
if (!isset($_SESSION["done_orders"])) $_SESSION["done_orders"] = [];

$show_form = isset($_POST['show_form']) || isset($_POST['submit_order']);
$success = '';
$error = '';

// Handle new order submission
if (isset($_POST['submit_order'])) {
    $table_number = trim($_POST["table_number"]);
    $order_details = trim($_POST["order_details"]);
    if (empty($table_number) || empty($order_details)) {
        $error = "Please fill in all fields.";
        $show_form = true;
    } else {
        $_SESSION["active_orders"][] = [
            "table" => $table_number,
            "details" => $order_details,
            "waiter" => $_SESSION["waiter_name"],
            "time" => date("Y-m-d H:i:s")
        ];
        $success = "Order added as active!";
        $show_form = false;
    }
}

// Handle marking order as served
if (isset($_POST['serve_order'])) {
    $order_index = intval($_POST['order_index']);
    if (isset($_SESSION["active_orders"][$order_index])) {
        $_SESSION["done_orders"][] = $_SESSION["active_orders"][$order_index];
        array_splice($_SESSION["active_orders"], $order_index, 1);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Order</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #89f7fe 0%, #66a6ff 100%);
            font-family: 'Montserrat', Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
        }
        .container {
            max-width: 540px;
            margin: 40px auto;
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
        }
        h1 {
            color: #222f3e;
            text-align: center;
            margin-bottom: 0.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        h2 {
            color: #0097e6;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.3rem;
        }
        .success {
            color: #44bd32;
            background: #eafaf1;
            border: 1px solid #44bd32;
            border-radius: 8px;
            padding: 0.7rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .error {
            color: #e84118;
            background: #fbeee6;
            border: 1px solid #e84118;
            border-radius: 8px;
            padding: 0.7rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        label {
            display: block;
            margin-top: 1.2rem;
            color: #353b48;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        select, textarea {
            width: 100%;
            padding: 0.7rem;
            margin-top: 0.3rem;
            border-radius: 10px;
            border: 1.5px solid #dcdde1;
            font-size: 1rem;
            background: #f1f2f6;
            transition: border 0.2s;
            font-family: inherit;
            box-shadow: 0 1px 4px rgba(44,62,80,0.04);
        }
        select:focus, textarea:focus {
            border-color: #0097e6;
            outline: none;
        }
        button, .add-btn {
            margin-top: 1.2rem;
            padding: 0.8rem;
            background: linear-gradient(90deg, #0097e6 0%, #00b894 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }
        button:hover, .add-btn:hover {
            background: linear-gradient(90deg, #00b894 0%, #0097e6 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .orders {
            margin-top: 2rem;
        }
        .order {
            background: #f1f2f6;
            padding: 1.2rem 1.4rem;
            border-radius: 14px;
            margin-bottom: 1.2rem;
            box-shadow: 0 2px 8px rgba(44,62,80,0.06);
            position: relative;
            border-left: 6px solid #0097e6;
        }
        .order.done {
            background: #dff9fb;
            border-left: 6px solid #44bd32;
        }
        .order-title {
            font-weight: bold;
            color: #0097e6;
            margin-bottom: 0.3rem;
            font-size: 1.1em;
        }
        .order-time {
            font-size: 0.95em;
            color: #888;
            margin-bottom: 0.5rem;
        }
        .order-details {
            margin-bottom: 0.7rem;
            color: #222f3e;
        }
        .order-waiter {
            font-size: 0.95em;
            color: #888;
        }
        .center {
            text-align: center;
        }
        .add-btn {
            display: block;
            width: 100%;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, #74ebd5 0%, #ACB6E5 100%);
            color: #273c75;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: 0.8rem;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .add-btn:hover {
            background: linear-gradient(90deg, #ACB6E5 0%, #74ebd5 100%);
            color: #4078c0;
        }
        .select-table {
            background: #fff;
            border: 1.5px solid #0097e6;
            color: #0097e6;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👨‍🍳 Welcome, <?php echo htmlspecialchars($_SESSION["waiter_name"]); ?>!</h1>
        <div class="center">
            <?php if (!$show_form): ?>
                <form method="post" style="display:inline;">
                    <button type="submit" name="show_form" class="add-btn">➕ Add Order</button>
                </form>
            <?php endif; ?>
        </div>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($show_form): ?>
            <form method="post">
                <label for="table_number">Table Number</label>
                <select name="table_number" id="table_number" class="select-table" required>
                    <option value="">Select table</option>
                    <?php
                    // Change 10 to however many tables you have
                    for ($i = 1; $i <= 10; $i++) {
                        echo "<option value=\"$i\">Table $i</option>";
                    }
                    ?>
                </select>
                <label for="order_details">Order Details</label>
                <textarea name="order_details" id="order_details" rows="3" required placeholder="e.g. 2x Burger, 1x Coke"></textarea>
                <button type="submit" name="submit_order">Submit Order</button>
            </form>
        <?php endif; ?>

        <div class="orders">
            <h2>🟢 Active Orders</h2>
            <?php
            // Group active orders by waiter
            $active_by_waiter = [];
            foreach ($_SESSION["active_orders"] as $i => $order) {
                $active_by_waiter[$order["waiter"]][] = ["index" => $i, "order" => $order];
            }
            if (empty($active_by_waiter)) {
                echo '<p class="center" style="color:#888;">No active orders.</p>';
            } else {
                foreach ($active_by_waiter as $waiter => $orders) {
                    echo '<h3 style="color:#4078c0; margin-top:1.5rem;">👤 ' . htmlspecialchars($waiter) . '</h3>';
                    foreach ($orders as $item) {
                        $i = $item["index"];
                        $order = $item["order"];
            ?>
                        <div class="order">
                            <div class="order-title">Table <?php echo htmlspecialchars($order["table"]); ?></div>
                            <div class="order-time"><?php echo htmlspecialchars($order["time"]); ?></div>
                            <div class="order-details"><?php echo nl2br(htmlspecialchars($order["details"])); ?></div>
                            <form method="post" style="margin-top:0.5rem;">
                                <input type="hidden" name="order_index" value="<?php echo $i; ?>">
                                <button type="submit" name="serve_order">Mark as Served</button>
                            </form>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>

        <div class="orders">
            <h2>✅ Done Orders</h2>
            <?php
            // Group done orders by waiter
            $done_by_waiter = [];
            foreach ($_SESSION["done_orders"] as $order) {
                $done_by_waiter[$order["waiter"]][] = $order;
            }
            if (empty($done_by_waiter)) {
                echo '<p class="center" style="color:#888;">No done orders.</p>';
            } else {
                foreach ($done_by_waiter as $waiter => $orders) {
                    echo '<h3 style="color:#4078c0; margin-top:1.5rem;">👤 ' . htmlspecialchars($waiter) . '</h3>';
                    foreach ($orders as $order) {
            ?>
                        <div class="order done">
                            <div class="order-title">Table <?php echo htmlspecialchars($order["table"]); ?></div>
                            <div class="order-time"><?php echo htmlspecialchars($order["time"]); ?></div>
                            <div class="order-details"><?php echo nl2br(htmlspecialchars($order["details"])); ?></div>
                            <div class="order-waiter"><em>Served by <?php echo htmlspecialchars($order["waiter"]); ?></em></div>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>
    </div>
</body>
</html>