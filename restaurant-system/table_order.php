<?php
session_start();

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $table_number = trim($_POST["table_number"]);
    $order_details = trim($_POST["order_details"]);
    if (empty($table_number) || empty($order_details)) {
        $error = "Please fill in all fields.";
    } else {
        // Save order to MySQL database as available
        $mysqli = new mysqli("localhost", "root", "", "hackathon_db");
        if ($mysqli->connect_errno) {
            $error = "Database connection failed.";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO orders (table_number, details, waiter, time, status) VALUES (?, ?, '', ?, 'available')");
            $now = date("Y-m-d H:i:s");
            $stmt->bind_param("sss", $table_number, $order_details, $now);
            if ($stmt->execute()) {
                $success = "Your order has been sent! A waiter will serve you soon.";
            } else {
                $error = "Failed to save order.";
            }
            $stmt->close();
            $mysqli->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Table Order</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #EAEFEF 0%, #B8CFCE 100%);
            font-family: 'Montserrat', Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
        }
        .order-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 3rem 2.5rem 2.5rem 2.5rem;
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(127,140,170,0.10);
            border: 2px solid #B8CFCE;
        }
        h2 {
            color: #333446;
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 2.2rem;
        }
        label {
            display: block;
            margin-top: 1.5rem;
            color: #7F8CAA;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 1.2rem;
        }
        select, textarea {
            width: 100%;
            padding: 1rem;
            margin-top: 0.5rem;
            border-radius: 12px;
            border: 1.5px solid #B8CFCE;
            font-size: 1.2rem;
            background: #EAEFEF;
            transition: border 0.2s;
            font-family: inherit;
            box-shadow: 0 1px 4px rgba(127,140,170,0.04);
            color: #333446;
        }
        select:focus, textarea:focus {
            border-color: #7F8CAA;
            outline: none;
        }
        button {
            margin-top: 2rem;
            padding: 1rem;
            background: linear-gradient(90deg, #7F8CAA 0%, #333446 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.3rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            box-shadow: 0 2px 8px rgba(51,52,70,0.08);
            width: 100%;
        }
        button:hover {
            background: linear-gradient(90deg, #333446 0%, #7F8CAA 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .success-message {
            color: #333446;
            background: #B8CFCE;
            border: 1px solid #7F8CAA;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.2rem;
        }
        .error-message {
            color: #e74c3c;
            background: #EAEFEF;
            border: 1px solid #e74c3c;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.2rem;
        }
        .menu-btn {
            flex: 1 1 40%;
            padding: 1rem;
            background: #B8CFCE;
            border: 1.5px solid #7F8CAA;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #333446;
            cursor: pointer;
            transition: background 0.2s, border 0.2s, color 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 2px 8px rgba(127,140,170,0.04);
        }
        .menu-btn:hover {
            background: #7F8CAA;
            border-color: #333446;
            color: #fff;
            transform: translateY(-2px) scale(1.03);
        }
        .qty-input {
            padding: 0.7rem;
            border: 1px solid #B8CFCE;
            border-radius: 8px;
            width: 70px;
            text-align: center;
            font-size: 1.1rem;
            margin-right: 0.7rem;
            background: #EAEFEF;
            color: #333446;
        }
        #clear-all {
            background: #273F4F;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.7rem 1.5rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(127,140,170,0.08);
            transition: background 0.2s, color 0.2s;
        }
        #clear-all:hover {
            background: #4E6688;
        }
        #selected-items div {
            background: #EAEFEF;
            color: #333446;
            font-size: 1.1rem;
        }
        @media (max-width: 900px) {
            .order-container {
                max-width: 98vw;
                padding: 1.5rem 0.5rem;
            }
            .menu-btn {
                font-size: 1.1rem;
                padding: 0.7rem;
            }
            select, textarea {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="order-container">
        <h2>Table Order</h2>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="table_order.php">
            <label for="table_number">Table Number</label>
            <select name="table_number" id="table_number" required>
                <option value="">Select table</option>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?php echo $i; ?>">Table <?php echo $i; ?></option>
                <?php endfor; ?>
            </select>

            <!-- Menu Section -->
            <label>Menu</label>
            <div id="menu-list" style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
                <?php
                $menu = [
                    ["Burger", "🍔", 6.50], ["Pizza", "🍕", 8.00], ["Salad", "🥗", 4.00], ["Fries", "🍟", 2.50], ["Steak", "🥩", 12.00], ["Sandwich", "🥪", 5.00],
                    ["Coke", "🥤", 1.50], ["Water", "💧", 1.00], ["Coffee", "☕", 2.00], ["Juice", "🧃", 2.50], ["Icecream", "🍨", 3.00]
                ];
                foreach ($menu as $item) {
                    $name = $item[0];
                    $icon = $item[1];
                    $price = $item[2];
                    ?>
                    <button type="button" class="menu-btn" data-item="<?php echo $name; ?>" data-price="<?php echo $price; ?>">
                        <?php echo $icon . " " . $name . " ($" . number_format($price, 2) . ")"; ?>
                    </button>
                <?php } ?>
            </div>

            <!-- Selected items list and clear all button -->
            <div id="selected-items" style="margin-bottom:1rem;"></div>
            <button type="button" id="clear-all">Clear All</button>

            <label for="order_details">Order Details</label>
            <textarea name="order_details" id="order_details" rows="3" required placeholder="e.g. 2x Burger, 1x Coke"></textarea>
            <button type="submit">Send Order</button>
        </form>

        <script>
            // Track quantities for each item
            const menuCounts = {};
            const menuIcons = {
                "Burger": "🍔", "Pizza": "🍕", "Salad": "🥗", "Fries": "🍟", "Steak": "🥩",
                "Sandwich": "🥪", "Coke": "🥤", "Water": "💧", "Coffee": "☕", "Juice": "🧃", "Icecream": "🍨"
            };

            // Add menuPrices for price lookup
            const menuPrices = {
                "Burger": 6.50, "Pizza": 8.00, "Salad": 4.00, "Fries": 2.50, "Steak": 12.00, "Sandwich": 5.00,
                "Coke": 1.50, "Water": 1.00, "Coffee": 2.00, "Juice": 2.50, "Icecream": 3.00
            };

            function updateOrderDetails() {
                let orderArr = [];
                let total = 0;
                for (let key in menuCounts) {
                    if (menuCounts[key] > 0) {
                        let price = 0;
                        if (menuPrices[key]) price = menuPrices[key];
                        orderArr.push(menuCounts[key] + "x " + key + " ($" + (price * menuCounts[key]).toFixed(2) + ")");
                        total += price * menuCounts[key];
                    }
                }
                document.getElementById('order_details').value = orderArr.join(', ');
                renderSelectedItems();
                document.getElementById('order_total').innerText = 'Total: $' + total.toFixed(2);
            }

            function renderSelectedItems() {
                const container = document.getElementById('selected-items');
                container.innerHTML = '';
                for (let key in menuCounts) {
                    if (menuCounts[key] > 0) {
                        const itemDiv = document.createElement('div');
                        itemDiv.style.display = 'inline-flex';
                        itemDiv.style.alignItems = 'center';
                        itemDiv.style.marginRight = '0.7rem';
                        itemDiv.style.marginBottom = '0.5rem';
                        itemDiv.style.background = '#f1f2f6';
                        itemDiv.style.borderRadius = '8px';
                        itemDiv.style.padding = '0.3rem 0.7rem';

                        itemDiv.innerHTML = `<span style="font-size:1.1em;margin-right:0.3em;">${menuIcons[key]}</span> <span>${menuCounts[key]}x ${key}</span>`;

                        const delBtn = document.createElement('button');
                        delBtn.type = 'button';
                        delBtn.textContent = '❌';
                        delBtn.style.marginLeft = '0.5em';
                        delBtn.style.background = 'none';
                        delBtn.style.border = 'none';
                        delBtn.style.color = '#e84118';
                        delBtn.style.fontSize = '1.1em';
                        delBtn.style.cursor = 'pointer';
                        delBtn.onclick = function() {
                            delete menuCounts[key];
                            updateOrderDetails();
                        };
                        itemDiv.appendChild(delBtn);
                        container.appendChild(itemDiv);
                    }
                }
            }

            document.querySelectorAll('.menu-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const item = btn.getAttribute('data-item');
                    menuCounts[item] = (menuCounts[item] || 0) + 1;
                    updateOrderDetails();
                });
            });

            document.getElementById('clear-all').onclick = function() {
                for (let key in menuCounts) {
                    delete menuCounts[key];
                }
                updateOrderDetails();
            };
        </script>
        <div id="order_total" style="font-size:1.2em; font-weight:bold; margin-top:1em; text-align:center;">Total: $0.00</div>
    </div>
</body>
</html>