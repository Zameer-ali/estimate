<?php

require_once("./db_connection.php");
// Validate required fields
if (!isset($_POST['password']) || !isset($_POST['username']) || empty($_POST['password']) || empty($_POST['username'])) {
    $response = array(
        'status' => 'Validation failed',
        'message' => 'All fields are required.',
        'details' => ""
    );
    $json = json_encode($response);

    // Set the content type to application/json
    header('Content-Type: application/json');
    http_response_code(422);

    // Output the JSON string
    echo $json;
    die;
}
// Get the input data from the request
$username = $_POST['username'];
$password = $_POST['password'];

// Check if the user exists in the database
$user_query = "SELECT * FROM users WHERE username = '$username'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

if ($user_data) {
    // Verify the password
    if (password_verify($password, $user_data['password'])) {
        // Generate a UUID string for the authentication token
        $authToken = 'uuid' . time() . rand(999999, 9999999) . uniqid() . rand(999999, 9999999);

        // Update the authentication token and expiry time in the database
        $expiryTime = date('Y-m-d H:i:s', strtotime('+24 hour')); // Set the expiry time to 1 hour from now
        $updateQuery = "UPDATE users SET auth_token = '$authToken', auth_token_expiry = '$expiryTime' WHERE username = '$username'";

        $user_data['auth_token'] = $authToken;
        $user_data['auth_token_expiry'] = $expiryTime;


        mysqli_query($conn, $updateQuery);
        mysqli_free_result($result);
        mysqli_close($conn);
        $response = array(
            'status' => 'success',
            'message' => 'Login successful.',
            'detail' => $user_data
        );
    } else {
        // Password doesn't match
        $response = array(
            'status' => 'error',
            'message' => 'Invalid username or password.'
        );
    }
} else {
    // User doesn't exist
    $response = array(
        'status' => 'error',
        'message' => 'Invalid username or password.'
    );
}

$json = json_encode($response);

// Set the content type to application/json
header('Content-Type: application/json');

// Output the JSON string
echo $json;
