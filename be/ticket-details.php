<?php
require_once('./connection.php');

header("Content-Type: application/json");

// Get input data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Support both POST (JSON) and GET for testing
$user_id = isset($data['user_id']) ? $data['user_id'] : (isset($_GET['user_id']) ? $_GET['user_id'] : null);
$date = isset($data['date']) ? $data['date'] : (isset($_GET['date']) ? $_GET['date'] : null);

if (!$user_id || !$date) {
    http_response_code(400);
    echo json_encode(['status' => false, 'msg' => 'User ID and Date are required']);
    exit;
}

// Format date to Y-m-d if it comes as d-m-Y
if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
    $date = date('Y-m-d', strtotime($date));
}

// Fetch tickets joined with time_slots and game_types
// PRIMARY SORT: Time DESC (Latest first), SECONDARY: Game Type
$sql = "SELECT t.*, ts.time as draw_time, gt.game_type_code 
        FROM tickets t
        LEFT JOIN time_slots ts ON t.time_slot_id = ts.time_slot_id
        LEFT JOIN game_types gt ON t.game_type_id = gt.game_type_id
        WHERE t.user_id = '$user_id' AND t.ticket_date = '$date' 
        ORDER BY ts.time DESC, t.game_type_id ASC, t.purchase_date ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['status' => false, 'msg' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$grouped_data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $draw_time = substr($row['draw_time'], 0, 5); // 09:30:00 -> 09:30
    $event_code = $row['ticket_date'] . ' ' . $draw_time;
    $gt_id = $row['game_type_id'];

    if (!isset($grouped_data[$event_code])) {
        $grouped_data[$event_code] = [
            'gifteventcode' => $event_code,
            'draw_time' => $draw_time,
            'event_total_amount' => 0,
            'game_types' => []
        ];
    }

    if (!isset($grouped_data[$event_code]['game_types'][$gt_id])) {
        $grouped_data[$event_code]['game_types'][$gt_id] = [
            'game_type_id' => $gt_id,
            'game_type_code' => $row['game_type_code'],
            'tickets' => []
        ];
    }

    // Process tickets according to game_id
    $n = $row['selected_number'];
    $tickets_to_add = [];

    if ($row['game_id'] == 2) {
        // Bulk B: Expansion - Divide amount by 10 for each part to keep total correct
        $part_amount = $row['amount'] / 10;
        for ($i = 0; $i <= 9; $i++) {
            $num = $i . $n;
            $tickets_to_add[] = [
                'number' => $num,
                'qty' => $row['qty'],
                'amount' => $part_amount,
                'rate' => $row['rate'],
                'game_id' => $row['game_id']
            ];
        }
    } else {
        // Bulk A or Loose
        $display_number = $n;
        if ($row['game_id'] == 1) {
            $display_number = "0$n-9$n";
        }
        $tickets_to_add[] = [
            'number' => $display_number,
            'qty' => $row['qty'],
            'amount' => $row['amount'],
            'rate' => $row['rate'],
            'game_id' => $row['game_id']
        ];
    }

    foreach ($tickets_to_add as $t) {
        $grouped_data[$event_code]['game_types'][$gt_id]['tickets'][] = $t;
    }

    $grouped_data[$event_code]['event_total_amount'] += $row['amount'];
}

// Convert to indexed array for response
$final_output = [];
foreach ($grouped_data as $e) {
    $formatted_game_types = [];
    foreach ($e['game_types'] as $gt) {
        $formatted_game_types[] = $gt;
    }
    $e['game_types'] = $formatted_game_types;
    $final_output[] = $e;
}

if (empty($final_output)) {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No records found for the given date']);
} else {
    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $final_output]);
}
?>