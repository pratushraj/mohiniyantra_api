<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
require_once('./connection.php');
date_default_timezone_set('Asia/Kolkata');

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
if (!$data || !isset($data['date'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => false, 'msg' => 'Invalid data']);
    exit;
}
// echo $date = date('Y-m-d', strtotime($data['date']));

$dateObject = DateTime::createFromFormat('d-m-Y', $data['date']);
if ($dateObject) {
    $date = $dateObject->format('Y-m-d');
} else {
    // Handle the error if the date is invalid
    $date = null;
}


$resultCountSql = mysqli_query($conn,"
    SELECT 
        MAX(game_type_id) AS game_type_id, 
        time_slot_id, 
        MAX(result_date) AS result_date
    FROM 
        results
    WHERE 
        result_date = '$date'
        AND game_id = 3
    GROUP BY 
        time_slot_id;
");
$events = [];
if(mysqli_num_rows($resultCountSql) > 0) {
    while ($resultCountRes = mysqli_fetch_assoc($resultCountSql)) {
        $time_slot_id = $resultCountRes['time_slot_id'];
        if($time_slot_id != 0) {
            $giftEventsSql = mysqli_query($conn, "SELECT t.time, CONCAT(gt.game_type_code, r.result_number) AS event_code
                FROM results r
                LEFT JOIN game_types gt ON gt.game_type_id = r.game_type_id
                LEFT JOIN time_slots t ON t.time_slot_id = r.time_slot_id
                WHERE r.time_slot_id = $time_slot_id
                AND r.result_date = '$date'
                AND r.game_id = 3");
        
            // Organize events by time slot
            while ($giftEventsRes = mysqli_fetch_assoc($giftEventsSql)) {
                $time = $giftEventsRes['time'];
                $event_code = $giftEventsRes['event_code'];
        
                $events[$time][] = $event_code;
            }
        }
    }

    // Transform events into desired format
    $formattedEvents = [];
    foreach ($events as $time => $eventCodes) {
        $formattedEvents[] = [
            $time => $eventCodes
        ];
    }

    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $formattedEvents]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No data found']);
    exit;
}


