<?php
require_once('./connection.php');

header("Content-Type: application/json");

// Get input data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Support both POST (JSON) and GET for testing
$user_id = isset($data['user_id']) ? $data['user_id'] : (isset($_GET['user_id']) ? $_GET['user_id'] : null);
$date = isset($data['date']) ? $data['date'] : (isset($_GET['date']) ? $_GET['date'] : null);
$time_slot_id = isset($data['time_slot_id']) ? $data['time_slot_id'] : (isset($_GET['time_slot_id']) ? $_GET['time_slot_id'] : null);

if (!$user_id || !$date) {
    http_response_code(400);
    echo json_encode(['status' => false, 'msg' => 'User ID and Date are required']);
    exit;
}

$slot_filter = $time_slot_id ? " AND time_slot_id = '$time_slot_id'" : "";

// Format date to Y-m-d if it comes as d-m-Y
if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
    $date = date('Y-m-d', strtotime($date));
}

// 1. FETCH RESULTS - only game_id = 3 for footer display
$results_map = [];
$res_query = "SELECT time_slot_id, game_type_id, game_id, result_number 
              FROM results 
              WHERE result_date = '$date' 
              AND result_number IS NOT NULL 
              AND result_number != '' 
              AND game_id = 3
              $slot_filter";
$res_sql = mysqli_query($conn, $res_query);
if ($res_sql) {
    while ($r = mysqli_fetch_assoc($res_sql)) {
        $results_map[$r['time_slot_id']][$r['game_type_id']] = $r['result_number'];
    }
}

// 2. FETCH USER WINNINGS
$winnings_map = [];
$win_query = "SELECT time_slot_id, game_type_id, game_id, amount, number_won 
              FROM user_winnings 
              WHERE user_id = '$user_id' 
              AND winning_date = '$date' 
              $slot_filter";
$win_sql = mysqli_query($conn, $win_query);
if ($win_sql) {
    while ($w = mysqli_fetch_assoc($win_sql)) {
        $key = $w['time_slot_id'] . '_' . $w['game_type_id'] . '_' . $w['game_id'] . '_' . $w['number_won'];
        $winnings_map[$key] = (double) $w['amount'];
    }
}

// 3. FETCH TICKETS
$sql = "SELECT t.*, ts.time as draw_time, gt.game_type_code 
        FROM tickets t
        LEFT JOIN time_slots ts ON t.time_slot_id = ts.time_slot_id
        LEFT JOIN game_types gt ON t.game_type_id = gt.game_type_id
        WHERE t.user_id = '$user_id' 
        AND t.ticket_date = '$date' 
        $slot_filter
        ORDER BY ts.time DESC, t.purchase_date DESC";

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
    $game_id = $row['game_id'];

    // Create unique batch key based on time_slot, game_type, AND purchase_date
    $batch_key = $ts_id . '_' . $gt_id . '_' . $purchase_time;

    if (!isset($grouped_data[$batch_key])) {
        $double_result = isset($results_map[$ts_id][$gt_id]) ? $results_map[$ts_id][$gt_id] : null;

        $grouped_data[$batch_key] = [
            'gifteventcode' => $event_code,
            'draw_time' => $draw_time,
            'purchase_date' => $purchase_time,
            'game_type_code' => $row['game_type_code'],
            'game_type_id' => $gt_id,
            'time_slot_id' => $ts_id,
            'winning_number' => $double_result,
            'tickets' => [],
            'batch_points' => 0,
            'batch_winning_amount' => 0,
            'cancel_amount' => 0,
            'batch_cancelled_points' => 0,
            'is_cancelled' => false,
            'has_cancelled_tickets' => false
        ];
    }

    // Process ticket based on game_id
    if ($game_id == 1) {
        // Side A - Single entry showing range
        $display_number = $row['selected_number'] . "0-" . $row['selected_number'] . "9";

        $win_amt = 0;
        // Check if game_id 3 result matches one of the derived numbers
        $double_result = isset($results_map[$ts_id][$gt_id]) ? $results_map[$ts_id][$gt_id] : null;
        if ($double_result !== null) {
            for ($i = 0; $i <= 9; $i++) {
                if ($double_result === $row['selected_number'] . $i) {
                    // Won on this part: qty * 10 * 10
                    // But effectively rate for Side A is 100 for winning? 
                    // Let's stick to the prompt: if number matches, qty * 100. 
                    // Note qty returned by DB might be original qty. Wait, prompt says: 
                    // "result mai game_id = 3 sab k liye same hai, ab uss tickket slot mai match kro ki number mil rha hai ki nhi, agar mil rha hai toh uske qty se *100 kar doh"
                    $win_amt += ((double) $row['qty'] * 10) * 10; // Side A parts are 10. so total qty * 10 * 10 = qty * 100. Let's do qty * 100 for side A total match qty. Wait. Side A win qty is original qty? If user selected Side A '5', numbers are 50..59. If result is 55, they win qty * 100.
                }
            }
        }

        // Correcting Side A logic according to "qty * 100" base instructions
        $win_amt = 0;
        if ($double_result !== null && substr($double_result, 0, 1) === $row['selected_number']) {
            $win_amt = ((double) $row['qty'] * 10) * 10; // Because 1 qty of Side A becomes 10qty underlying
        }

        $ticket_data = [
            'number' => $display_number,
            'qty' => (double) $row['qty'] * 10,
            'amount' => (double) $row['amount'],
            'rate' => (double) $row['rate'],
            'game_id' => $game_id,
            'status' => (int) $row['status'],
            'selected_number' => $row['selected_number'],
            'win_amount' => $win_amt,
            'is_cancelled' => ($row['status'] == 0) ? true : false,
            'cancelled_at' => $row['cancelled_at']
        ];

        // Update batch totals
        if ($ticket_data['is_cancelled']) {
            $grouped_data[$batch_key]['cancel_amount'] += $ticket_data['amount'];
            $grouped_data[$batch_key]['has_cancelled_tickets'] = true;
        } else {
            $grouped_data[$batch_key]['batch_points'] += $ticket_data['amount'];
            $grouped_data[$batch_key]['batch_winning_amount'] += $ticket_data['win_amount'];
        }

        $grouped_data[$batch_key]['tickets'][] = $ticket_data;

    } else if ($game_id == 2) {
        // Side B - Split into 10 individual numbers
        $part_amount = $row['amount'] / 10;
        $qty = (double) $row['qty'];
        $double_result = isset($results_map[$ts_id][$gt_id]) ? $results_map[$ts_id][$gt_id] : null;

        for ($i = 0; $i <= 9; $i++) {
            $full_number = $i . $row['selected_number'];

            $win_amt = 0;
            if ($double_result !== null && $double_result === $full_number) {
                $win_amt = $qty * 100;
            }

            $ticket_data = [
                'number' => $full_number,
                'qty' => $qty,
                'amount' => $part_amount,
                'rate' => (double) $row['rate'],
                'game_id' => $game_id,
                'status' => (int) $row['status'],
                'selected_number' => $row['selected_number'],
                'win_amount' => $win_amt,
                'is_cancelled' => ($row['status'] == 0) ? true : false,
                'cancelled_at' => $row['cancelled_at']
            ];

            // Update batch totals
            if ($ticket_data['is_cancelled']) {
                $grouped_data[$batch_key]['cancel_amount'] += $ticket_data['amount'];
                $grouped_data[$batch_key]['has_cancelled_tickets'] = true;
            } else {
                $grouped_data[$batch_key]['batch_points'] += $ticket_data['amount'];
                $grouped_data[$batch_key]['batch_winning_amount'] += $ticket_data['win_amount'];
            }

            $grouped_data[$batch_key]['tickets'][] = $ticket_data;
        }

    } else if ($game_id == 3) {
        // Double Game
        $double_result = isset($results_map[$ts_id][$gt_id]) ? $results_map[$ts_id][$gt_id] : null;

        $win_amt = 0;
        if ($double_result !== null && $double_result === $row['selected_number']) {
            $win_amt = (double) $row['qty'] * 100;
        }

        $ticket_data = [
            'number' => $row['selected_number'],
            'qty' => (double) $row['qty'],
            'amount' => (double) $row['amount'],
            'rate' => (double) $row['rate'],
            'game_id' => $game_id,
            'status' => (int) $row['status'],
            'selected_number' => $row['selected_number'],
            'win_amount' => $win_amt,
            'is_cancelled' => ($row['status'] == 0) ? true : false,
            'cancelled_at' => $row['cancelled_at']
        ];

        // Update batch totals
        if ($ticket_data['is_cancelled']) {
            $grouped_data[$batch_key]['cancel_amount'] += $ticket_data['amount'];
            $grouped_data[$batch_key]['has_cancelled_tickets'] = true;
        } else {
            $grouped_data[$batch_key]['batch_points'] += $ticket_data['amount'];
            $grouped_data[$batch_key]['batch_winning_amount'] += $ticket_data['win_amount'];
        }

        $grouped_data[$batch_key]['tickets'][] = $ticket_data;
    }
}

// Final processing for batch level flags and display
foreach ($grouped_data as &$batch) {
    // If all points are cancelled, mark whole batch as cancelled
    // But for report.html, just ensure cancel_amount is present
    $batch['is_cancelled'] = ($batch['cancel_amount'] > 0);

    if ($batch['is_cancelled']) {
        foreach ($batch['tickets'] as $t) {
            if ($t['is_cancelled'] && !empty($t['cancelled_at'])) {
                $batch['cancel_time'] = $t['cancelled_at'];
                break;
            }
        }
        if (!isset($batch['cancel_time'])) {
            $batch['cancel_time'] = $batch['purchase_date'];
        }
    }
}

$final_output = array_values($grouped_data);

if (empty($final_output)) {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No records found for the given date']);
} else {
    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $final_output]);
}
?>