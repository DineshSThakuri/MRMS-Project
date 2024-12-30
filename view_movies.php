<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Handle movie deletion
if (isset($_GET['delete'])) {
    $movie_id = intval($_GET['delete']);
    $sql = "DELETE FROM movie WHERE MovieID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $movie_id);
    if ($stmt->execute()) {
        $message = "Movie deleted successfully!";
    } else {
        $message = "Error deleting movie: " . $conn->error;
    }
    $stmt->close();
}

// Handle movie update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $movie_id = intval($_POST['movie_id']);
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $year = $_POST['year'];
    $description = $_POST['description'];

    $sql = "UPDATE movie SET Title = ?, Genre = ?, Year = ?, Description = ? WHERE MovieID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $title, $genre, $year, $description, $movie_id);
    if ($stmt->execute()) {
        $message = "Movie updated successfully!";
    } else {
        $message = "Error updating movie: " . $conn->error;
    }
    $stmt->close();
}

$sql = "SELECT * FROM movie";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Movies</title>
    <link rel="stylesheet" href="../css/view_movies.css">
</head>

<body>
    <div class="container">
        <h2>Movies</h2>
        <?php if (isset($message)): ?>
            <p style="color: green;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Genre</th>
                <th>Year</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actions</th> <!-- New Actions Column -->
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <form method="POST" action="">
                        <td><input type="text" name="title" value="<?= htmlspecialchars($row['Title']) ?>" required></td>
                        <td><input type="text" name="genre" value="<?= htmlspecialchars($row['Genre']) ?>" required></td>
                        <td><input type="number" name="year" value="<?= htmlspecialchars($row['Year']) ?>" required></td>
                        <td><textarea name="description"><?= htmlspecialchars($row['Description']) ?></textarea></td>
                        <td>
                            <?php if (!empty($row['Image'])): ?>
                                <img src="<?= htmlspecialchars($row['Image']) ?>" alt="Movie Image"
                                    style="width:100px;height:auto;">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Hidden field to store the movie ID -->
                            <input type="hidden" name="movie_id" value="<?= $row['MovieID'] ?>">
                            <!-- Update and Delete Buttons -->
                            <a href="update_movie.php?id=<?= $row['MovieID'] ?>" name="update"
                                class="btn btn-primary">Update</a>


                            <a href="?delete=<?= $row['MovieID'] ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this movie?');">Delete</a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </table>
        <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>

</html>

<?php mysqli_close($conn); ?>