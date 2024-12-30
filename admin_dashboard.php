<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: .login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>

<body>
    <div class="container">
        <h1>Welcome to the Admin Dashboard</h1>
        <div class="buttons">
            <a href="add_movie.php">Add Movie</a>
            <a href="view_movies.php">View Movies</a>
            <a href="../user/user_view_rating_review.php">View Rating And Review</a>
            <a href="view_users.php">View Users</a>
        </div>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>

</html>