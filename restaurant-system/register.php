<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register Waiter</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>📝 Waiter Registration</h1>
  <form action="save_user.php" method="post" class="login-form">
    <input type="text" name="username" placeholder="Choose username" required><br>
    <input type="password" name="password" placeholder="Choose password" required><br>
    <button type="submit">Register</button>
    <p><a href="index.html">← Back to Login</a></p>
    <?php
      if (isset($_GET['error'])) echo "<p style='color:red;'>Username already exists</p>";
      if (isset($_GET['success'])) echo "<p style='color:green;'>Account created! You can now log in.</p>";
    ?>
  </form>
</body>
</html>
