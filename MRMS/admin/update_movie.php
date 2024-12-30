<?php
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$movie_id = $_GET['id'] ?? null;  // Get movie ID from query string
$message = "";

// Fetch existing movie details if an ID is provided
if ($movie_id) {
    // Fetch the movie details from the database
    $result = mysqli_query($conn, "SELECT * FROM movie WHERE MovieID = '$movie_id'");
    $movie = mysqli_fetch_assoc($result);

    if (!$movie) {
        $message = "Movie not found!";
    }
} else {
    $message = "No movie selected to update.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $movie_id) {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $year = $_POST['year'];
    $description = $_POST['description'];
    $target_file = $movie['Image']; // Default to the current image

    // Handle the image upload if a new file is provided
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../image/";  // Define image directory
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;

        // Allow certain file formats (only jpg, jpeg, png, and gif)
        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // If no errors, attempt to upload the image
        if ($uploadOk && move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Remove old image if a new one is uploaded successfully
            if ($movie['Image'] && file_exists($movie['Image'])) {
                unlink($movie['Image']);
            }
        } else {
            $target_file = $movie['Image']; // Revert to old image if upload fails
            $message = "Sorry, there was an error uploading your file.";
        }
    }

    // Update the movie details in the database
    $sql = "UPDATE movie SET Title='$title', Genre='$genre', Year='$year', Description='$description', Image='$target_file' WHERE MovieID='$movie_id'";
    if (mysqli_query($conn, $sql)) {
        $message = "Movie updated successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Movie</title>
    <link rel="stylesheet" href="../css/add_movie.css">
</head>

<body>
    <div class="container">
        <h2>Update Movie</h2>

        <!-- Display success or error message -->
        <?php if ($message): ?>
            <p style="color: red;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <!-- If movie exists, show the form to update movie -->
        <?php if ($movie_id && $movie): ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="title" value="<?= htmlspecialchars($movie['Title']) ?>" placeholder="Title"
                    required>
                <input type="text" name="genre" value="<?= htmlspecialchars($movie['Genre']) ?>" placeholder="Genre"
                    required>
                <input type="number" name="year" value="<?= htmlspecialchars($movie['Year']) ?>" placeholder="Year"
                    required>
                <input type="file" name="image" placeholder="Image">
                <textarea name="description" placeholder="Description"
                    required><?= htmlspecialchars($movie['Description']) ?></textarea>
                <button type="submit">Update Movie</button>
            </form>
        <?php else: ?>
            <p>No movie selected for updating. Please select a movie from the <a href="admin_dashboard.php">dashboard</a>.
            </p>
        <?php endif; ?>

        <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>

</html>