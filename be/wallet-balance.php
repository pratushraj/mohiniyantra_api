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

if (!$data || !isset($data['userId'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => false, 'msg' => 'Invalid data 1']);
    exit;
}
$userId = $data['userId'];

$wallertBalanceRes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT wallet_balance FROM users WHERE id = $userId"));

http_response_code(200);
echo json_encode(['status' => true, 'msg' => 'Success', 'data'=> $wallertBalanceRes['wallet_balance']]);
exit;

?>