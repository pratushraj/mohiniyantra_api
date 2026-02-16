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
if (!$data || !isset($data['userId']) || !isset($data['requested-balance']) || !isset($data['uniqueId'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => false, 'msg' => 'Invalid data']);
    exit;
}

// Sanitize input data
$inputUserId = mysqli_real_escape_string($conn, $data['userId']);
$uniqueId = mysqli_real_escape_string($conn, $data['uniqueId']);
$requested_balance = mysqli_real_escape_string($conn, $data['requested-balance']);

$sql = mysqli_query($conn, "INSERT INTO recharge_request (user_id, user_unique_id, amount, requested_time) VALUES ($inputUserId, '$uniqueId', '$requested_balance', '".date('Y-m-d H:i:s')."')");

if ($sql) {
    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Rechage requested successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => false, 'msg' => 'Failed to insert: ' . mysqli_error($conn)]);
}