<?php
require_once('./connection.php');
date_default_timezone_set('Asia/Kolkata');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

$userId = isset($_GET['userId']) ? $_GET['userId'] : null;
$drawDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

if (!$userId) {
    echo json_encode(['status' => false, 'msg' => 'userId is required']);
    exit;
}

$query = "SELECT time_slot_id FROM user_selected_slots WHERE user_id = '$userId' AND draw_date = '$drawDate'";
$result = mysqli_query($conn, $query);

$slots = [];
while ($row = mysqli_fetch_assoc($result)) {
    $slots[] = $row['time_slot_id'];
}

echo json_encode(['status' => true, 'data' => $slots]);
?>