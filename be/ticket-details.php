<?php
require_once('./connection.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET");

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
$sql = "SELECT t.*, ts.time as draw_time, gt.game_type_code 
        FROM tickets t
        LEFT JOIN time_slots ts ON t.time_slot_id = ts.time_slot_id
        LEFT JOIN game_types gt ON t.game_type_id = gt.game_type_id
        WHERE t.user_id = '$user_id' AND t.ticket_date = '$date' 
        ORDER BY t.purchase_date DESC, ts.time ASC, t.game_id ASC, t.game_type_id ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['status' => false, 'msg' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$purchases = [];

while ($row = mysqli_fetch_assoc($result)) {
    $p_date = $row['purchase_date'];

    // Format time to show range (e.g., 12:00 to 13:00)
    $ts = strtotime($row['draw_time']);
    $to_time = date('H:i', $ts);
    $from_time = date('H:i', strtotime('-1 hour', $ts));
    $time_range = $from_time . ' to ' . $to_time;

    $event_code = $row['ticket_date'] . ' ' . $to_time;
    $game_key = $row['game_id'] . '_' . $row['game_type_id'];

    if (!isset($purchases[$p_date])) {
        $purchases[$p_date] = [
            'purchase_date' => $p_date,
            'user_id' => $row['user_id'],
            'total_purchase_amount' => 0,
            'events' => []
        ];
    }

    if (!isset($purchases[$p_date]['events'][$event_code])) {
        $purchases[$p_date]['events'][$event_code] = [
            'gifteventcode' => $event_code,
            'ticket_date' => $row['ticket_date'],
            'draw_time' => $to_time,
            'time_range' => $time_range,
            'time_slot_id' => $row['time_slot_id'],
            'numberselected' => []
        ];
    }

    if (!isset($purchases[$p_date]['events'][$event_code]['numberselected'][$game_key])) {
        $purchases[$p_date]['events'][$event_code]['numberselected'][$game_key] = [
            'game_type' => $row['game_type_id'],
            'game_type_code' => $row['game_type_code'],
            'game_id' => $row['game_id'],
            'totalamount' => 0,
            'selectednumbers' => []
        ];
    }

    // Map and Add ticket detail
    $n = $row['selected_number'];

    if ($row['game_id'] == 2) {
        // Bulk B: Return 10 separate entries in the array
        for ($i = 0; $i <= 9; $i++) {
            $num = $i . $n; // e.g., 0X, 1X, 2X...
            $purchases[$p_date]['events'][$event_code]['numberselected'][$game_key]['selectednumbers'][] = [
                'number' => $num,
                'qty' => $row['qty'],
                'amount' => $row['amount'],
                'rate' => $row['rate']
            ];
        }
    } else {
        // Bulk A (game_id 1) or Loose (game_id 3)
        $display_number = $row['selected_number'];
        if ($row['game_id'] == 1) {
            $display_number = "0$n-9$n";
        }

        $purchases[$p_date]['events'][$event_code]['numberselected'][$game_key]['selectednumbers'][] = [
            'number' => $display_number,
            'qty' => $row['qty'],
            'amount' => $row['amount'],
            'rate' => $row['rate']
        ];
    }

    // Update totals
    $purchases[$p_date]['events'][$event_code]['numberselected'][$game_key]['totalamount'] += $row['amount'];
    $purchases[$p_date]['total_purchase_amount'] += $row['amount'];
}

// Re-format to indexed arrays instead of associative keys for JSON compatibility
$final_output = [];
foreach ($purchases as $p) {
    $formatted_events = [];
    foreach ($p['events'] as $e) {
        $formatted_games = [];
        foreach ($e['numberselected'] as $g) {
            $formatted_games[] = $g;
        }
        $e['numberselected'] = $formatted_games;
        $formatted_events[] = $e;
    }
    $p['events'] = $formatted_events;
    $final_output[] = $p;
}

if (empty($final_output)) {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No records found for the given date']);
} else {
    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $final_output]);
}
?>