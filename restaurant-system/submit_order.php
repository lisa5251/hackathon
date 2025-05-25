<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $table = htmlspecialchars($_POST["table"]);
    $items = htmlspecialchars($_POST["items"]);
    $time = date("Y-m-d H:i:s");

    $entry = "[$time] Table $table: $items\n";

    file_put_contents("orders.txt", $entry, FILE_APPEND | LOCK_EX);

    echo "✅ Order for table $table saved!";
} else {
    echo "❌ Invalid request method.";
}
?>
