<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    // Sanitize user inputs
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $email = htmlspecialchars($_POST['email']);
    $confirm_password = htmlspecialchars($_POST['confirm_password']); // If you're adding password confirmation

    // Check if the password length is at least 8 characters
    if (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        // Check if passwords match
        $error_message = "Passwords do not match!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Validate email format
        $error_message = "Invalid email format!";
    } else {
        // Check if the user already exists
        $check_user_query = "SELECT * FROM user WHERE Username = ? OR Email = ?";
        $stmt = mysqli_prepare($conn, $check_user_query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // User already exists, display error
            $error_message = "Error: Username or Email already exists!";
        } else {
            // Encrypt the password before storing it in the database
            $hashed_password = $password;

            // Insert new user into the database
            $sql = "INSERT INTO user (Username, Password, Email) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $email);

            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Registration successful!";
                header("Location: login.php"); // Redirect to login page after successful registration
                exit();
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../css/signup.css">
</head>

<body>
    <div class="container">
        <h2>Sign Up</h2>

        <!-- Display error or success messages -->
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php elseif (!empty($success_message)): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <!-- Confirm password -->
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>

</html>