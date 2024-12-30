<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user is an admin
    $adminSql = "SELECT * FROM admin WHERE Username='$username' AND Password='$password'";
    $adminResult = mysqli_query($conn, $adminSql);

    if (mysqli_num_rows($adminResult) > 0) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'admin';
        header("Location:admin_dashboard.php");
        exit();
    }


    // Invalid login
    $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
