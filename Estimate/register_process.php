<?php

require_once("./db_connection.php");
// Validate required fields
if (!isset($_POST['first_name']) || !isset($_POST['last_name']) || !isset($_POST['password']) || !isset($_POST['email']) || !isset($_POST['contact']) || !isset($_POST['gender']) || !isset($_POST['username'])) {
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
// Validate input data
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$password = $_POST['password'];
$email = $_POST['email'];
$contact = $_POST['contact'];
$gender = $_POST['gender'];
$username = $_POST['username'];

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($password) || empty($email) || empty($contact) || empty($gender) || empty($username)) {
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

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

// Validate username
if (!preg_match("/^[a-zA-Z0-9_]{5,20}$/", $username)) {
    die("Username must be between 5 and 20 characters and can only contain letters, numbers and underscores.");
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Upload profile image
$profile_image = "";
if (!empty($_FILES['profile_image']['name'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_extensions = array("jpg", "jpeg", "png");
    if (!in_array($imageFileType, $allowed_extensions)) {
        die("Only JPG, JPEG, PNG files are allowed.");
    }
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        die("Error uploading file.");
    }
    $profile_image = $target_file;
}

// Check if the username already exists
$username_check_query = "SELECT * FROM users WHERE username = '$username'";
$username_check_result = mysqli_query($conn, $username_check_query);
if (mysqli_num_rows($username_check_result) > 0) {
    // Create a JSON response
    $response = array(
        'status' => 'error',
        'message' => 'Username already exists.',
    );
    $json = json_encode($response);

    // Set the content type to application/json
    http_response_code(400); // Set the status code to 400 Bad Request
    header('Content-Type: application/json');

    // Output the JSON string
    echo $json;
    die;
}
// Insert user into database
$sql = "INSERT INTO users (first_name, last_name, password, email, contact, profile_image, gender, username) VALUES ('$first_name', '$last_name', '$hashed_password', '$email', '$contact', '$profile_image', '$gender', '$username')";
if (mysqli_query($conn, $sql)) {
    // Get the inserted user data
    $user_id = mysqli_insert_id($conn);
    $user_query = "SELECT * FROM users WHERE id = $user_id";
    $user_result = mysqli_query($conn, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);

    // Create a JSON response
    $response = array(
        'status' => 'success',
        'message' => 'Registration successful.',
        'detail' => $user_data
    );
    $json = json_encode($response);

    // Set the content type to application/json
    header('Content-Type: application/json');

    // Output the JSON string
    echo $json;
} else {
    $response = array(
        'status' => 'success',
        'message' => "Error: " . $sql . "<br>" . mysqli_error($conn),
        'detail' => $user_data
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