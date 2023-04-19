<?php
require_once('./db_connection.php');
$headers = getallheaders();
if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $authToken = $matches[1];
        if (!empty($authToken)) {
            $query = "SELECT * FROM users WHERE auth_token = '$authToken' AND auth_token_expiry > NOW()";
            $result = mysqli_query($conn, $query);
            $user_data = mysqli_fetch_assoc($result);
            // Check if the query returned any results
            if ($result && mysqli_num_rows($result) > 0) {
                $response = array(
                    'status' => '',
                    // 'message' => 'Registration successful.',
                    'detail' => $user_data
                );
                $json = json_encode($response);
            
                // Set the content type to application/json
                header('Content-Type: application/json');
            
                // Output the JSON string
                // echo $json;
                // exit();
              
                // print_r($user_data);
                // User is authenticated and the token has not expired
                // Proceed with the requested operation
            } else {
                // Authentication failed or token has expired
                http_response_code(401);
                echo json_encode(array('status' => 401, 'message' => 'Unauthorized'));
                exit();
            }

        } else {
            // Authentication token is missing
            http_response_code(401);
            echo json_encode(array('status' => 401, 'message' => 'Unauthorized'));
            exit();
        }
    } else {
        // Authentication token is missing
        http_response_code(401);
        echo json_encode(array('status' => 401, 'message' => 'Unauthorized'));
        exit();
    }
} else {
    // Authentication token is missing
    http_response_code(401);
    echo json_encode(array('status' => 401, 'message' => 'Unauthorized'));
    exit();
}