<?php
require_once('./connection.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // Method not allowed
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validate the input data
if (!$data || !isset($data['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => false, 'msg' => 'Invalid data']);
    exit;
}

// Sanitize input data
$userId = mysqli_real_escape_string($conn, $data['id']);

// Fetch the user from the database
$userUpdateSql = mysqli_query($conn, "UPDATE users SET is_logged_in = 0 WHERE id = $userId");
if( $userUpdateSql ) {
    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Successfully logged out']);
} else {
    http_response_code(400);
    echo json_encode(['status' => false, 'msg' => 'Something went error']);
}



mysqli_close($conn); // Close the DB connection
