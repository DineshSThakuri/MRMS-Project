<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include './db.php';
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user is a regular user
    $userSql = "SELECT * FROM user WHERE Username='$username' AND Password='$password'";
    $userResult = mysqli_query($conn, $userSql);

    if (mysqli_num_rows($userResult) > 0) {
        $row = $userResult->fetch_assoc();
        $_SESSION['UserID'] = $row["UserID"];

        header("Location:user_view_movie.php");
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
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
</body>

</html>