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

// 1. Fetch available results for this date to display in report
$results_map = [];
$res_query = "SELECT time_slot_id, game_type_id, result_number FROM results WHERE result_date = '$date'";
$res_sql = mysqli_query($conn, $res_query);
if ($res_sql) {
    while ($r = mysqli_fetch_assoc($res_sql)) {
        $results_map[$r['time_slot_id']][$r['game_type_id']] = $r['result_number'];
    }
}

// 2. Fetch tickets joined with time_slots and game_types
$sql = "SELECT t.*, ts.time as draw_time, gt.game_type_code 
        FROM tickets t
        LEFT JOIN time_slots ts ON t.time_slot_id = ts.time_slot_id
        LEFT JOIN game_types gt ON t.game_type_id = gt.game_type_id
        WHERE t.user_id = '$user_id' AND t.ticket_date = '$date' 
        ORDER BY ts.time DESC, t.purchase_date DESC, t.game_id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['status' => false, 'msg' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$grouped_data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $draw_time = substr($row['draw_time'], 0, 5);
    $event_code = $row['ticket_date'] . ' ' . $draw_time;
    $purchase_time = $row['purchase_date'];
    $gt_id = $row['game_type_id'];
    $ts_id = $row['time_slot_id'];

    $batch_key = $event_code . '_' . $purchase_time . '_' . $gt_id;

    if (!isset($grouped_data[$batch_key])) {
        // Find result if published
        $winning_no = isset($results_map[$ts_id][$gt_id]) ? $results_map[$ts_id][$gt_id] : null;

        $grouped_data[$batch_key] = [
            'gifteventcode' => $event_code,
            'draw_time' => $draw_time,
            'purchase_date' => $purchase_time,
            'game_type_code' => $row['game_type_code'],
            'game_type_id' => $gt_id,
            'winning_number' => $winning_no,
            'batch_winning_amount' => 0, // Track total win for this batch
            'tickets' => []
        ];
    }

    $n = $row['selected_number'];
    $tickets_to_add = [];
    $win_no = $grouped_data[$batch_key]['winning_number'];

    if ($row['game_id'] == 2) {
        // Bulk B: Expansion
        $part_amount = $row['amount'] / 10;
        for ($i = 0; $i <= 9; $i++) {
            $num = $i . $n;

            // Winning check
            $win_amt = 0;
            if ($win_no !== null && $num == $win_no) {
                $win_amt = $part_amount * 90;
            }

            $tickets_to_add[] = [
                'number' => $num,
                'qty' => $row['qty'],
                'amount' => $part_amount,
                'rate' => $row['rate'],
                'game_id' => $row['game_id'],
                'win_amount' => $win_amt
            ];
            $grouped_data[$batch_key]['batch_winning_amount'] += $win_amt;
        }
    } else {
        // Bulk A or Loose
        $display_number = $n;
        if ($row['game_id'] == 1) {
            $display_number = "0$n-9$n";
        }

        // Winning check (Bulk A covers multiple numbers, we check which one wins)
        $win_amt = 0;
        if ($win_no !== null) {
            if ($row['game_id'] == 1) {
                // If the second digit matches the selected index $n
                if (strlen($win_no) == 2 && substr($win_no, 1, 1) == $n) {
                    $win_amt = $row['amount'] / 10 * 90; // total / 10 * 90 = total * 9
                }
            } else if ($row['game_id'] == 3) {
                // Loose:Direct match
                if ($n == $win_no) {
                    $win_amt = $row['amount'] * 90;
                }
            }
        }

        $tickets_to_add[] = [
            'number' => $display_number,
            'qty' => $row['qty'],
            'amount' => $row['amount'],
            'rate' => $row['rate'],
            'game_id' => $row['game_id'],
            'win_amount' => $win_amt
        ];
        $grouped_data[$batch_key]['batch_winning_amount'] += $win_amt;
    }

    foreach ($tickets_to_add as $t) {
        $grouped_data[$batch_key]['tickets'][] = $t;
    }
}

// Convert to simple indexed array
$final_output = array_values($grouped_data);

if (empty($final_output)) {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No records found for the given date']);
} else {
    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $final_output]);
}
?>