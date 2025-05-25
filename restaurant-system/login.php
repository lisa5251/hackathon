<?php
session_start();

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Manager login (only Lisa Suliqi)
    if ($username === "Lisa Suliqi" && $password === "managerpass") {
        $_SESSION["logged_in"] = true;
        $_SESSION["username"] = $username;
        $_SESSION["role"] = "manager";
        header("Location: manager_dashboard.php");
        exit();
    }

    // Waiter/Chef login using MySQL
    $mysqli = new mysqli("localhost", "root", "", "hackathon_db");
    if ($mysqli->connect_errno) {
        $error = "Database connection failed.";
    } else {
        $stmt = $mysqli->prepare("SELECT username, `password`, role FROM users WHERE username = ? LIMIT 1");
        if (!$stmt) {
            $error = "Prepare failed: " . $mysqli->error;
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($dbUser, $dbHash, $dbRole);
                $stmt->fetch();
                if (password_verify($password, $dbHash)) {
                    $_SESSION["logged_in"] = true;
                    $_SESSION["username"] = $dbUser;
                    $_SESSION["role"] = $dbRole;
                    if ($dbRole === "waiter") {
                        $_SESSION["waiter_name"] = $dbUser;
                        header("Location: waiter_dashboard.php");
                        exit();
                    } elseif ($dbRole === "chef") {
                        $_SESSION["chef_name"] = $dbUser;
                        header("Location: chef_dashboard.php");
                        exit();
                    }
                } else {
                    $error = "Invalid credentials.";
                }
            } else {
                $error = "Invalid credentials.";
            }
            $stmt->close();
        }
        $mysqli->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign In</title>
    <style>
        body { background: #f5f6fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-container { background: #fff; padding: 2rem 2.5rem; border-radius: 12px; box-shadow: 0 4px 24px rgba(44, 62, 80, 0.12); width: 340px; }
        .login-container h2 { margin-bottom: 1.5rem; color: #273c75; text-align: center; }
        .login-container label { display: block; margin-bottom: 0.5rem; color: #353b48; }
        .login-container input[type="text"],
        .login-container input[type="password"] { width: 100%; padding: 0.7rem; margin-bottom: 1.2rem; border: 1px solid #dcdde1; border-radius: 6px; font-size: 1rem; background: #f1f2f6; transition: border 0.2s; }
        .login-container input:focus { border-color: #4078c0; outline: none; }
        .login-container button { width: 100%; padding: 0.8rem; background: #4078c0; color: #fff; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        .login-container button:hover { background: #273c75; }
        .error-message { color: #e84118; background: #fbeee6; border: 1px solid #e84118; border-radius: 6px; padding: 0.7rem; margin-bottom: 1rem; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Sign In</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="login.php">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>
