<?php
// Start session
session_start();

// Include database connection
require_once 'db_connect.php';

// Fetch recommended movies from the database
$query = "SELECT * FROM movies ORDER BY rating DESC LIMIT 10";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Recommendation System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
</head>

<body>
    <header>
        <h1>Welcome to Movie Recommendations</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="recommendations.php">Recommendations</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Top Recommended Movies</h2>
        <div class="movie-container">
            <?php
            if ($result->num_rows > 0) {
                while ($movie = $result->fetch_assoc()) {
                    echo "<div class='movie-card'>";
                    echo "<img src='uploads/" . htmlspecialchars($movie['image']) . "' alt='" . htmlspecialchars($movie['title']) . "'>";
                    echo "<h3>" . htmlspecialchars($movie['title']) . "</h3>";
                    echo "<p>Genre: " . htmlspecialchars($movie['genre']) . "</p>";
                    echo "<p>Rating: " . htmlspecialchars($movie['rating']) . "/10</p>";
                    echo "<p>" . htmlspecialchars($movie['description']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No recommendations available at the moment.</p>";
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Movie Recommendation System. All rights reserved.</p>
    </footer>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>