<?php
session_start();
if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"] || $_SESSION["role"] !== "manager" || $_SESSION["username"] !== "Lisa Suliqi") {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];
    if ($username === "Lisa Suliqi" && $password === "managerpass") {
        // Always log in as manager if Lisa Suliqi and correct password
        $_SESSION["logged_in"] = true;
        $_SESSION["username"] = "Lisa Suliqi";
        $_SESSION["role"] = "manager";
        header("Location: manager_dashboard.php");
        exit();
    }
    if (empty($username) || empty($password) || !in_array($role, ["waiter", "chef"])) {
        $error = "All fields are required.";
    } else if ($role === "manager") {
        $error = "You cannot register a manager. Only Lisa Suliqi is the manager.";
    } else {
        $mysqli = new mysqli("localhost", "root", "", "hackathon_db");
        if ($mysqli->connect_errno) {
            $error = "Database connection failed.";
        } else {
            // Check if username already exists (regardless of role)
            $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = "This username is already taken. Please choose another.";
            } else {
                // If not found, register and log in
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $mysqli->prepare("INSERT INTO users (username, `password`, role) VALUES (?, ?, ?)");
                $insert->bind_param("sss", $username, $hash, $role);
                if ($insert->execute()) {
                    $_SESSION["logged_in"] = true;
                    $_SESSION["username"] = $username;
                    $_SESSION["role"] = $role;
                    if ($role === "waiter") {
                        $_SESSION["waiter_name"] = $username;
                        header("Location: waiter_dashboard.php");
                        exit();
                    } elseif ($role === "chef") {
                        $_SESSION["chef_name"] = $username;
                        header("Location: chef_dashboard.php");
                        exit();
                    }
                } else {
                    $error = "Failed to register user.";
                }
                $insert->close();
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
    <title>Register Worker</title>
    <style>
        body { background: #f5f6fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .register-container { background: #fff; padding: 2rem 2.5rem; border-radius: 12px; box-shadow: 0 4px 24px rgba(44, 62, 80, 0.12); width: 340px; }
        .register-container h2 { margin-bottom: 1.5rem; color: #273c75; text-align: center; }
        .register-container label { display: block; margin-bottom: 0.5rem; color: #353b48; }
        .register-container input[type="text"],
        .register-container input[type="password"],
        .register-container select { width: 100%; padding: 0.7rem; margin-bottom: 1.2rem; border: 1px solid #dcdde1; border-radius: 6px; font-size: 1rem; background: #f1f2f6; transition: border 0.2s; }
        .register-container input:focus, .register-container select:focus { border-color: #4078c0; outline: none; }
        .register-container button { width: 100%; padding: 0.8rem; background: #4078c0; color: #fff; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        .register-container button:hover { background: #273c75; }
        .error-message { color: #e84118; background: #fbeee6; border: 1px solid #e84118; border-radius: 6px; padding: 0.7rem; margin-bottom: 1rem; text-align: center; }
        .success-message { color: #44bd32; background: #eafaf1; border: 1px solid #44bd32; border-radius: 6px; padding: 0.7rem; margin-bottom: 1rem; text-align: center; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register Worker</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="post" action="register_worker.php">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="waiter">Waiter</option>
                <option value="chef">Chef</option>
            </select>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
