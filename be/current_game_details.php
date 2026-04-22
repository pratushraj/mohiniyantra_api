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
    http_response_code(405);
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

$currentGameTimeSlotRes = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT * FROM time_slots WHERE time > '$currentTime' ORDER BY time ASC LIMIT 1;"));

if (isset($currentGameTimeSlotRes) && !empty($currentGameTimeSlotRes)) {
    $gameDetails['timeSlotId'] = $currentGameTimeSlotRes['time_slot_id'];
    $gameDetails['end_time'] = $currentGameTimeSlotRes['time'];
    $gameDetails['date'] = date('Y-m-d');
} else {
    // Take the first slot of the day from DB instead of hardcoded 08:30
    $firstSlot = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM time_slots ORDER BY time ASC LIMIT 1"));
    if ($firstSlot) {
        $gameDetails['timeSlotId'] = $firstSlot['time_slot_id'];
        $gameDetails['end_time'] = $firstSlot['time'];
    } else {
        $gameDetails['timeSlotId'] = 1;
        $gameDetails['end_time'] = '08:00:00';
    }
    $gameDetails['date'] = date('Y-m-d', strtotime('+1 day'));
}

// Fetch Previous Game Results using dynamic time_slot_game_config
$latestResult = mysqli_fetch_assoc(mysqli_query($conn, "SELECT result_date, time_slot_id FROM results WHERE game_id = 3 ORDER BY result_id DESC LIMIT 1"));

$prevGameResults = [];
if ($latestResult) {
    $ldate = $latestResult['result_date'];
    $lsid = $latestResult['time_slot_id'];

    $previousGameResultsSql = mysqli_query($conn, "
        SELECT 
            cfg.game_type_name,
            CONCAT(cfg.game_type_code, LPAD(IFNULL(r.result_number, '--'), 2, '0')) AS win_code, 
            TIME_FORMAT(IFNULL(t.time, '00:00:00'), '%H:%i') AS time 
        FROM time_slot_game_config cfg
        LEFT JOIN results r ON r.time_slot_id = cfg.time_slot_id AND r.game_type_id = cfg.game_type_idx AND r.game_id = 3 AND r.result_date = '$ldate'
        LEFT JOIN time_slots t ON t.time_slot_id = cfg.time_slot_id
        WHERE cfg.time_slot_id = $lsid AND cfg.is_active = 1
        ORDER BY cfg.game_type_idx ASC;
    ");

    while ($row = mysqli_fetch_assoc($previousGameResultsSql)) {
        if (strpos($row['win_code'], '--') !== false) {
            $row['win_code'] = str_replace('0--', '--', $row['win_code']);
        }
        $prevGameResults[] = $row;
    }
}

$gameDetails['prev_game_results'] = $prevGameResults;

http_response_code(200);
echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $gameDetails]);
exit;
?>