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
$userId = $data['id'];
$sql = mysqli_query($conn, "
    SELECT user_id, amount, winning_date, g.game_name, gt.game_type_name, gt.game_type_code, t.time, number_won 
    FROM user_winnings uw
    LEFT JOIN games g ON uw.game_id = g.game_id
    LEFT JOIN game_types gt ON uw.game_type_id = gt.game_type_id
    LEFT JOIN time_slots t ON uw.time_slot_id = t.time_slot_id
    WHERE user_id = ".$userId."
    ORDER BY id DESC;
");
if( mysqli_num_rows($sql) > 0 ) {
    while( $res = mysqli_fetch_assoc($sql) ) {
        $events[] = $res;
    }
    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $events]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No Record Found', 'data' => $events]);
    exit;
}


?>