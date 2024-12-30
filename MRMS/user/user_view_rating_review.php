<link href="../css/view_rate_review_movie.css" rel="stylesheet">

<table class="table">
    <thead>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Movie Name</th>
            <th scope="col">UserID</th>
            <th scope="col">Rating</th>
            <th scope="col">Review</th>
            <th scope="col">Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include("../db.php"); // Ensure the DB connection is included correctly
        
        // Query to fetch reviews and movie names, sorted by rating in descending order
        $ssql = "
            SELECT mr.id, m.Title AS movie_name, mr.UserID, mr.rating, mr.review, mr.created_at
            FROM movie_reviews mr
            JOIN movie m ON mr.MovieID = m.MovieID
            ORDER BY mr.rating DESC
        ";

        // Execute the query
        $results = $conn->query($ssql);

        // Check if results are available
        if ($results->num_rows > 0) {
            // Loop through the results
            while ($row = $results->fetch_assoc()) {
                // Check if keys exist before displaying them to avoid undefined array key warnings
                $movieName = ($row['movie_name']) ? ($row['movie_name']) : 'N/A';
                $userId = ($row['UserID']) ? ($row['UserID']) : 'N/A';
                $rating = ($row['rating']) ? ($row['rating']) : 'N/A';
                $review = ($row['review']) ? ($row['review']) : 'N/A';
                $createdAt = ($row['created_at']) ? ($row['created_at']) : 'N/A';

                echo "
                    <tr>
                        <td>{$row['id']}</td>
                        <td>{$movieName}</td>
                        <td>{$userId}</td>
                        <td>{$rating}</td>
                        <td>{$review}</td>
                        <td>{$createdAt}</td>
                    </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='6'>No reviews found.</td></tr>";
        }
        ?>
    </tbody>
</table>