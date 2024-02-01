<?php
// Include your config.php file to establish a database connection
include "config.php";

// Set response headers for CORS (Cross-Origin Resource Sharing) and JSON content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE'); // Allow only DELETE requests
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Retrieve JSON data from the request body and decode it
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if the required data field 'id' is present in the JSON data
    if (isset($data['id'])) {
        // Sanitize the 'id' field to prevent SQL injection
        $id = mysqli_real_escape_string($conn, $data['id']);

        // Build the SQL query using prepared statements to prevent SQL injection
        $sql = "DELETE FROM `data` WHERE `id` = ?";

        // Prepare the SQL statement
        $stmt = mysqli_prepare($conn, $sql);

        // Bind the 'id' parameter and set its value
        mysqli_stmt_bind_param($stmt, "i", $id);

        // Execute the SQL statement
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(array('message' => 'Data deleted from the database', 'status' => 'true'));
        } else {
            echo json_encode(array('message' => 'Data is not deleted from the database', 'status' => 'false'));
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(array('message' => 'Missing required data field "id"', 'status' => 'false'));
    }
} else {
    echo json_encode(array('message' => 'Invalid request method', 'status' => 'false'));
}

// Close the database connection
mysqli_close($conn);
?>
