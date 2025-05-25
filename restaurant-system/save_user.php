<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $usersFile = "users.txt";

    if (!file_exists($usersFile)) {
        file_put_contents($usersFile, "");
    }

    $lines = file($usersFile, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        list($storedUser, $storedHash) = explode(":", $line);
        if ($storedUser === $username) {
            header("Location: register.php?error=1");
            exit();
        }
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents($usersFile, "$username:$hashed\n", FILE_APPEND);
    header("Location: register.php?success=1");
    exit();
}
?>
