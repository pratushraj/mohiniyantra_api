<?php
require_once('./connection.php');
date_default_timezone_set('Asia/Kolkata');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405); // Method not allowed
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

$currentTime = date('H:i:s');

$upcomingEventsSql = mysqli_query($conn, "SELECT * FROM time_slots WHERE time > '$currentTime'");

if (mysqli_num_rows($upcomingEventsSql) > 0) {
    $events = [];
    while ($row = mysqli_fetch_assoc($upcomingEventsSql)) {
        $events[] = $row;
    }

    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $events]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No data found']);
    exit;
}
