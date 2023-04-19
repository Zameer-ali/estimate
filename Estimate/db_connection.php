<?php
// Connect to database
error_reporting(E_ERROR);
$conn = mysqli_connect("localhost", "root", "", "estimate");
if (!$conn) {
     // Create a JSON response
     $response = array(
        'status' => 'error',
        'message' => 'Registration successful.',
        'details' => "Connection failed: " . mysqli_connect_error()
    );
    $json = json_encode($response);

    // Set the content type to application/json and HTTP status code to 500
    header('Content-Type: application/json');
    http_response_code(500);

    // Output the JSON string
    echo $json;
    die;
}
?>
