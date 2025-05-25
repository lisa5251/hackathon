<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $lines = file("users.txt", FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        list($storedUser, $storedHash) = explode(":", $line);
        if ($storedUser === $username && password_verify($password, $storedHash)) {
            $_SESSION["logged_in"] = true;
            $_SESSION["username"] = $username;
            header("Location: dashboard.php");
            exit();
        }
    }
    header("Location: index.html?error=1");
    exit();
}
?>
