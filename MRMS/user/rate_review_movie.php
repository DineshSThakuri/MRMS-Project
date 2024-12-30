<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include './db.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    die("User is not logged in. Please log in to rate and review movies.");
}

// Initialize error and success messages
$error_message = null;
$success_message = null;
$has_reviewed = false; // Variable to check if the user has reviewed the movie

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieID = $_POST['MovieID'];
    $userID = $_SESSION['UserID'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    // Validate rating (must be between 1 and 5) and non-empty review
    if ($rating < 1 || $rating > 5) {
        $error_message = "Invalid rating. Please select a value between 1 and 5.";
    } elseif (empty($review)) {
        $error_message = "Please write a review.";
    } else {
        // Check if the user has already reviewed this movie
        $check_stmt = $conn->prepare("SELECT * FROM movie_reviews WHERE MovieID = ? AND UserID = ?");
        $check_stmt->bind_param("ii", $movieID, $userID);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // User has already reviewed this movie
            $has_reviewed = true;
            $error_message = "You have already rated and reviewed this movie. You can only submit one review per movie.";
        } else {
            // Insert the new rating and review
            $stmt = $conn->prepare("INSERT INTO movie_reviews (MovieID, UserID, rating, review) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iiis", $movieID, $userID, $rating, $review);

                // Execute the query
                if ($stmt->execute()) {
                    $success_message = "Your rating and review were submitted successfully!";
                } else {
                    $error_message = "Error submitting rating and review: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Error preparing statement: " . $conn->error;
            }
        }
        $check_stmt->close();
    }
}

// Check if the user has already rated and reviewed the movie
$movieID = isset($_GET['MovieID']) ? $_GET['MovieID'] : null; // Get MovieID from URL or form (depending on your setup)
$userID = $_SESSION['UserID'];
if ($movieID) {
    $check_stmt = $conn->prepare("SELECT * FROM movie_reviews WHERE MovieID = ? AND UserID = ?");
    $check_stmt->bind_param("ii", $movieID, $userID);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        // User has already reviewed this movie
        $has_reviewed = true;
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate and Review Movie</title>
    <link rel="stylesheet" href="../css/rate_review_movie.css" />
</head>

<body>
    <div class="container">
        <h2>Rate and Review a Movie</h2>

        <!-- Display error message -->
        <?php if ($error_message): ?>
            <p class="error"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <!-- Display success message -->
        <?php if ($success_message): ?>
            <p class="success"><?= htmlspecialchars($success_message) ?></p>
        <?php endif; ?>

        <!-- If the user has already reviewed, don't display the form -->
        <?php if (!$has_reviewed): ?>
            <!-- Rating and review form -->
            <form action="rate_review_movie.php" method="POST" class="rate-form">
                <label for="MovieID">Select Movie:</label>
                <select name="MovieID" id="MovieID" required>
                    <?php
                    // Fetch movies from the database
                    $result = $conn->query("SELECT MovieID, Title FROM movie");

                    if ($result) {
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['MovieID'] . '">' . htmlspecialchars($row['Title']) . '</option>';
                            }
                        } else {
                            echo '<option>No movies available</option>';
                        }
                        $result->free();
                    } else {
                        echo '<option>Error fetching movies</option>';
                    }
                    ?>
                </select>

                <label for="rating">Your Rating (1-5):</label>
                <div class="star-rating" style="display:flex; flex-direction:row-reverse">
                    <input type="radio" id="star1" name="rating" value="1" required><label for="star1">&#9733;</label>
                    <input type="radio" id="star2" name="rating" value="2"><label for="star2">&#9733;</label>
                    <input type="radio" id="star3" name="rating" value="3"><label for="star3">&#9733;</label>
                    <input type="radio" id="star4" name="rating" value="4"><label for="star4">&#9733;</label>
                    <input type="radio" id="star5" name="rating" value="5"><label for="star5">&#9733;</label>
                </div>

                <label for="review">Your Review:</label>
                <textarea name="review" rows="5" required></textarea>

                <button type="submit" class="submit-btn">Submit Rating and Review</button>
            </form>
        <?php else: ?>
            <p class="info">You have already rated and reviewed this movie. Thank you!</p>
        <?php endif; ?>

        <a href="user_view_movie.php" class="back-link">Back to Movies List</a>
    </div>
</body>

</html>

<?php
// Close database connection at the end of the script
$conn->close();
?>