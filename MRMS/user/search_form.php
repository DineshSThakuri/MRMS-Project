<link rel="stylesheet" href="../css/search_form.css" />

<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mrms"; // Update this to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search query from the form
$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';

// Initialize an array to store search results
$searchResults = [];

if ($searchQuery != '') {
    // Fetch all movies from the database
    $sql = "SELECT MovieID, Title, Description, Image FROM movie"; // Adjust table name/fields
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $title = $row['Title'];
            $description = $row['Description'];
            $image = $row['Image']; // Fetch image path

            // Calculate similarity between the search query and the movie title
            $levenshteinDistance = levenshtein(strtolower($searchQuery), strtolower($title));
            $similarityPercent = similar_text(strtolower($searchQuery), strtolower($title));

            // Determine threshold for displaying results (tune as needed)
            if ($levenshteinDistance < 5 || $similarityPercent > 50) {
                // Store matching results
                $searchResults[] = [
                    'MovieID' => $row['MovieID'],
                    'Title' => $row['Title'],
                    'Description' => $row['Description'],
                    'Image' => $image,
                ];
            }
        }

        // Sort search results by Levenshtein distance or similarity
        usort($searchResults, function ($a, $b) {
            return $a['LevenshteinDistance'] - $b['LevenshteinDistance'];
        });
    } else {
        echo "No movies found in the database.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="../css/search_form.css">
</head>

<body>
    <h1>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h1>

    <?php if (!empty($searchResults)): ?>
        <ul>
            <?php foreach ($searchResults as $movie): ?>
                <li>
                    <div class="movie-container">
                        <!-- Display movie image thumbnail -->
                        <div class="movie-image">
                            <img src="<?php echo htmlspecialchars($movie['Image']); ?>" alt="Movie Image" />
                        </div>

                        <!-- Display movie details (title, description, etc.) -->
                        <div class="movie-details">
                            <h3><?php echo htmlspecialchars($movie['Title']); ?></h3>
                            <p><?php echo htmlspecialchars($movie['Description']); ?></p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No movies matched your search.</p>
    <?php endif; ?>

</body>

</html>