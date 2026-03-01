<?php
require_once('./connection.php');
date_default_timezone_set('Asia/Kolkata');
$currentTime = date('H:i:s');
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405); // Method not allowed
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}
// die('ok');
$currentGameTimeSlotRes = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT * FROM time_slots WHERE time > '$currentTime' ORDER BY time ASC LIMIT 1;"));

if (isset($currentGameTimeSlotRes) && !empty($currentGameTimeSlotRes)) {
    $gameDetails['timeSlotId'] = $currentGameTimeSlotRes['time_slot_id'];
    $gameDetails['end_time'] = $currentGameTimeSlotRes['time'];
    $gameDetails['date'] = date('Y-m-d');
} else {
    $gameDetails['timeSlotId'] = 1;
    $gameDetails['end_time'] = '08:30:00';
    $gameDetails['date'] = date('Y-m-d', strtotime('+1 day'));
}


$previousGameResultsSql = mysqli_query($conn, "
    SELECT 
        gt.game_type_name,
        CONCAT(gt.game_type_code, IFNULL(r.result_number, '--')) AS win_code, 
        TIME_FORMAT(IFNULL(t.time, '00:00:00'), '%H:%i') AS time 
    FROM game_types gt
    LEFT JOIN results r ON r.game_type_id = gt.game_type_id AND r.game_id = 3 
        AND r.result_date = (SELECT result_date FROM results WHERE game_id = 3 ORDER BY result_id DESC LIMIT 1)
        AND r.time_slot_id = (SELECT time_slot_id FROM results WHERE game_id = 3 ORDER BY result_id DESC LIMIT 1)
    LEFT JOIN time_slots t ON r.time_slot_id = t.time_slot_id
    ORDER BY gt.game_type_id ASC;
");
$prevGameResults = [];
while ($previousGameResultsRes = mysqli_fetch_assoc($previousGameResultsSql)) {
    $prevGameResults[] = $previousGameResultsRes;
}

$gameDetails['prev_game_results'] = $prevGameResults;
// echo json_encode($prevGameResults);

http_response_code(200);
echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $gameDetails]);
exit;
