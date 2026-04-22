<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('./connection.php');
date_default_timezone_set('Asia/Kolkata');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data || !isset($data['date'])) {
    http_response_code(400);
    echo json_encode(['status' => false, 'msg' => 'Invalid data']);
    exit;
}

$dateObject = DateTime::createFromFormat('d-m-Y', $data['date']);
if ($dateObject) {
    $date = $dateObject->format('Y-m-d');
} else {
    $date = null;
}

$resultCountSql = mysqli_query($conn, "
    SELECT 
        time_slot_id
    FROM 
        results
    WHERE 
        result_date = '$date'
        AND game_id = 3
    GROUP BY 
        time_slot_id
    ORDER BY time_slot_id DESC;
");

$events = [];
if (mysqli_num_rows($resultCountSql) > 0) {
    while ($resultCountRes = mysqli_fetch_assoc($resultCountSql)) {
        $time_slot_id = $resultCountRes['time_slot_id'];
        if ($time_slot_id != 0) {
            // Updated to join with time_slot_game_config for dynamic codes
            $giftEventsSql = mysqli_query($conn, "
                SELECT t.time, CONCAT(IFNULL(cfg.game_type_code, '??'), LPAD(IFNULL(r.result_number, '--'), 2, '0')) AS event_code
                FROM results r
                LEFT JOIN time_slot_game_config cfg ON cfg.time_slot_id = r.time_slot_id AND cfg.game_type_idx = r.game_type_id
                LEFT JOIN time_slots t ON t.time_slot_id = r.time_slot_id
                WHERE r.time_slot_id = $time_slot_id
                AND r.result_date = '$date'
                AND r.game_id = 3
                ORDER BY r.game_type_id ASC");

            while ($giftEventsRes = mysqli_fetch_assoc($giftEventsSql)) {
                $time = $giftEventsRes['time'];
                $event_code = $giftEventsRes['event_code'];
                $events[$time][] = $event_code;
            }
        }
    }

    $formattedEvents = [];
    foreach ($events as $time => $eventCodes) {
        $formattedEvents[] = [$time => $eventCodes];
    }

    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $formattedEvents]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No data found']);
    exit;
}
?>