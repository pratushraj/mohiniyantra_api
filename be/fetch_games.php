<?php
require_once('./connection.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

$currentTime = date('H:i:s');
$timeSlotId = isset($_GET['timeSlotId']) ? (int) $_GET['timeSlotId'] : null;

if (!$timeSlotId) {
    // Dynamically find the NEXT slot
    $ts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT time_slot_id FROM time_slots WHERE time > '$currentTime' ORDER BY time ASC LIMIT 1"));

    // If no next slot today, find the FIRST slot for tomorrow
    if (!$ts) {
        $ts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT time_slot_id FROM time_slots ORDER BY time ASC LIMIT 1"));
    }

    $timeSlotId = $ts['time_slot_id'] ?? 1;
}

// Fetch the actual time label for this slot from DB
$slotInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT time FROM time_slots WHERE time_slot_id = $timeSlotId"));
$slotTime = $slotInfo ? date('H:i', strtotime($slotInfo['time'])) : '--:--';

// Fetch active games for the specific timing
$sql = "SELECT game_type_idx as id, game_type_name as name, game_type_code as code 
        FROM time_slot_game_config 
        WHERE time_slot_id = $timeSlotId AND is_active = 1 
        ORDER BY game_type_idx ASC";

$result = mysqli_query($conn, $sql);

if ($result) {
    $gameTypes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $gameTypes[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'code' => $row['code'],
            'time' => $slotTime
        ];
    }

    http_response_code(200);
    echo json_encode([
        'status' => true,
        'msg' => 'Success',
        'data' => $gameTypes,
        'timeSlotId' => $timeSlotId,
        'timeLabel' => $slotTime
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'msg' => 'Database error',
        'error' => mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>