<?php 
// PHP code to generate a unique token and store it in the database
require_once("./db_connection.php");

// Get the input data from the API request
$email = $_POST['email'];

// Check if the user exists in the database
$user_query = "SELECT * FROM users WHERE email = '$email'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

if ($user_data) {
  // Generate a unique token
  $token = bin2hex(random_bytes(32));

  // Calculate the expiration time (24 hours from now)
  $expiration_time = time() + 24 * 60 * 60;

  // Store the token in the database
  $insert_query = "INSERT INTO password_reset_tokens (email, token, expiration_time) VALUES ('$email', '$token', $expiration_time)";
  mysqli_query($conn, $insert_query);

  // Send the email to the user
  $to = $email;
  $subject = "Reset your password";
  $message = "Please click on the following link to reset your password: http://example.com/reset_password.php?token=$token";
  $headers = "From: Your Website <noreply@yourwebsite.com>";
  mail($to, $subject, $message, $headers);

  // Create a JSON response
  $response = array(
    'status' => 'success',
    'message' => 'An email has been sent to your email address with instructions on how to reset your password.',
    'detail' => '',
  );
} else {
  // User doesn't exist
  $response = array(
    'status' => 'error',
    'message' => 'The email address you entered is not registered.',
    'detail' => ''
  );
}