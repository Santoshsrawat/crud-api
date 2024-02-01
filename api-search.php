<?php
// Include your config.php file to establish a database connection
include "config.php";

// Set response headers for CORS (Cross-Origin Resource Sharing) and JSON content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST'); // Allow only POST requests
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve JSON data from the request body
    $postData = file_get_contents('php://input');
    
    // Decode JSON data
    $requestData = json_decode($postData, true);

    // Check if 'search' key exists in the JSON data
    if (isset($requestData['search'])) {
        // Retrieve the search term
        $searchTerm = mysqli_real_escape_string($conn, $requestData['search']);
        
        // Build the SQL query using prepared statements to prevent SQL injection
        $sql = "SELECT * FROM `data` WHERE `name` LIKE ? OR `email` LIKE ?";
        
        // Prepare the SQL statement
        $stmt = mysqli_prepare($conn, $sql);
        
        // Bind parameters and set their values
        $searchKeyword = "%$searchTerm%";
        mysqli_stmt_bind_param($stmt, "ss", $searchKeyword, $searchKeyword);
        
        // Execute the SQL statement
        mysqli_stmt_execute($stmt);
        
        // Get the result set
        $result = mysqli_stmt_get_result($stmt);
        
        // Initialize an array to store the search results
        $searchResults = array();
        
        // Fetch and store the results in the array
        while ($row = mysqli_fetch_assoc($result)) {
            $searchResults[] = $row;
        }
        
        // Close the prepared statement
        mysqli_stmt_close($stmt);
        
        // Check if there are search results
        if (!empty($searchResults)) {
            // Return the search results as JSON
            echo json_encode($searchResults);
            exit; // Exit to prevent further output
        }
    }
}

// If no results were found or there was an error, return a message
echo json_encode(array('message' => 'No results found', 'status' => 'false'));

// Close the database connection
mysqli_close($conn);
?>
