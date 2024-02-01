<?php
// Include your database connection configuration
include "config.php";

// Set the response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Initialize a variable to store the result
$response = array();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data from the request body and decode it
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if 'sid' is provided in the JSON data
    if (isset($data['sid'])) {
        // Sanitize the 'sid' value to prevent SQL injection
        $my_data = mysqli_real_escape_string($conn, $data['sid']);

        // Construct the SQL query
        $sql = "SELECT * FROM `data` WHERE id = '{$my_data}'";

        // Execute the SQL query
        $result = mysqli_query($conn, $sql);

        if ($result) {
            // Check if any rows were found
            if (mysqli_num_rows($result) > 0) {
                // Fetch the data as an associative array
                $output = mysqli_fetch_assoc($result);
                $response['data'] = $output;
            } else {
                // No record found
                $response['message'] = 'No record found';
                $response['status'] = 'false';
            }
        } else {
            // SQL query execution failed
            $response['message'] = 'SQL query failed: ' . mysqli_error($conn);
            $response['status'] = 'false';
        }
    } else {
        // 'sid' not provided in the JSON data
        $response['message'] = 'No "sid" provided in the JSON request.';
        $response['status'] = 'false';
    }
} else {
    // Invalid HTTP method
    $response['message'] = 'Invalid request method. Please use POST.';
    $response['status'] = 'false';
}

// Encode the response as JSON and echo it
echo json_encode($response);
?>
