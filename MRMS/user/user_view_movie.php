<?php
session_start();
include './db.php';

// Initialize $movies array and $error_message
$movies = [];
$error_message = null;

// Fetch all movies
$stmt = $conn->prepare("SELECT * FROM movie");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any movies were found
    if ($result->num_rows > 0) {
        $movies = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $error_message = "No movies found.";
    }

    $stmt->close();
} else {
    $error_message = "Error preparing statement: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies List</title>
    <link rel="stylesheet" href="../css/user_view_movie.css">
</head>

<body>
    <header style="
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100vw;
    height:100px;
    padding:0 2rem;
">
        <h2>MRMS</h2>
        <div>

            <img src="../image/avatar.jpg" alt="" style="
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 0px;
    ">
            <div>
                <a href="logout.php" class="back-link" style="
    margin: 0px;
    ">logout</a>
            </div>
        </div>
    </header>
    <div class="container">
        <h2>Movies List</h2>
        <?php if (!empty($movies)): ?>
            <ul>
                <?php foreach ($movies as $movie): ?>
                    <li>
                        <?php if (!empty($movie['Image'])): ?>
                            <img src="<?= htmlspecialchars($movie['Image']) ?>" alt="Movie Image">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($movie['Title']) ?></h3>
                        <p><strong>Category:</strong> <?= htmlspecialchars($movie['Genre']) ?></p>
                        <p><strong>Year:</strong> <?= htmlspecialchars($movie['Year']) ?></p>
                        <p><strong>Description:</strong> <?= htmlspecialchars($movie['Description']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><?= htmlspecialchars($error_message) ?: 'An error occurred.' ?></p>
        <?php endif; ?>

        <a href="rate_review_movie.php" class="back-link">Rate movie</a>
        <a href="user_view_rating_review.php" class="back-link">View Rating</a>
        <a href="../index.php" class="back-link">Back to Home</a>


    </div>
</body>

</html>