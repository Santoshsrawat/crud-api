<?php
// Include your config.php file to establish a database connection
include "config.php";

// Set response headers for CORS (Cross-Origin Resource Sharing) and JSON content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST'); // Fixed the method name
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With'); // Fixed the header name

// Retrieve JSON data from the request body and decode it
$data = json_decode(file_get_contents("php://input"), true);

// Check if the required data fields are present in the JSON data
if (isset($data['name']) && isset($data['phone']) && isset($data['email'])) {
    // Sanitize the input data to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $data['name']);
    $phone = mysqli_real_escape_string($conn, $data['phone']);
    $email = mysqli_real_escape_string($conn, $data['email']);

    // Build the SQL query using prepared statements to prevent SQL injection
    $sql = "INSERT INTO `data`(`name`, `phone`, `email`) VALUES (?, ?, ?)";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind parameters and set their values
    mysqli_stmt_bind_param($stmt, "sss", $name, $phone, $email);

    // Execute the SQL statement
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(array('message' => 'Data inserted into the database', 'status' => 'true'));
    } else {
        echo json_encode(array('message' => 'Data is not inserted into the database', 'status' => 'false'));
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(array('message' => 'Missing required data fields', 'status' => 'false'));
}

// Close the database connection
mysqli_close($conn);
?>
