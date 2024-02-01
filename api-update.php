<?php
// Include your config.php file to establish a database connection
include "config.php";

// Set response headers for CORS (Cross-Origin Resource Sharing) and JSON content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: put'); // Fixed the method name
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With'); // Fixed the header name

// Retrieve JSON data from the request body and decode it
$data = json_decode(file_get_contents("php://input"), true);

// Check if the required data fields are present in the JSON data
if (isset($data['id']) && isset($data['name']) && isset($data['phone']) && isset($data['email'])) {
    // Sanitize the input data to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $data['id']);
    $name = mysqli_real_escape_string($conn, $data['name']);
    $phone = mysqli_real_escape_string($conn, $data['phone']);
    $email = mysqli_real_escape_string($conn, $data['email']);

    // Build the SQL query using prepared statements to prevent SQL injection
    $sql = "UPDATE `data` SET `name`=?, `phone`=?, `email`=? WHERE `id`=?";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind parameters and set their values
    mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $email, $id);

    // Execute the SQL statement
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(array('message' => 'Data updated in the database', 'status' => 'true'));
    } else {
        echo json_encode(array('message' => 'Data is not updated in the database', 'status' => 'false'));
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(array('message' => 'Missing required data fields', 'status' => 'false'));
}

// Close the database connection
mysqli_close($conn);
?>
