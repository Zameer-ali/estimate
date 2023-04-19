<?php

require_once("./db_connection.php");
require_once("./authentication.php");
// Get user ID from request

// Validate required fields
if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['contact']) || empty($_POST['gender'])) {
    $response = array(
        'status' => 'Validation failed',
        'message' => 'User ID, First Name, Last Name, Email, Contact, and Gender are required fields.',
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

// Validate email
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

// Update user in database
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$contact = $_POST['contact'];
$gender = $_POST['gender'];

$update_query = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', email = '$email', contact = '$contact', gender = '$gender' WHERE auth_token = '$authToken'";

if (mysqli_query($conn, $update_query)) {
    // Get the updated user data
    $user_query = "SELECT * FROM users WHERE auth_token = '$authToken'";
    $user_result = mysqli_query($conn, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);

    // Create a JSON response
    $response = array(
        'status' => 'success',
        'message' => 'Profile updated successfully.',
        'detail' => $user_data
    );
    $json = json_encode($response);

    // Set the content type to application/json
    header('Content-Type: application/json');

    // Output the JSON string
    echo $json;
} else {
    $response = array(
        'status' => 'error',
        'message' => "Error updating profile: " . mysqli_error($conn),
    );
    $json = json_encode($response);

    // Set the content type to application/json
    header('Content-Type: application/json');

    // Output the JSON string
    echo $json;
}

// Close database connection
mysqli_close($conn);
?>
