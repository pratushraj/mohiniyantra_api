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

// 1. Fetch available results for this date (All game types including Single and Double)
$results_map = [];
$res_query = "SELECT time_slot_id, game_type_id, game_id, result_number FROM results WHERE result_date = '$date' AND result_number IS NOT NULL AND result_number != ''";
$res_sql = mysqli_query($conn, $res_query);
if ($res_sql) {
    while ($r = mysqli_fetch_assoc($res_sql)) {
        // Store by slot, type, and game for precise matching
        $results_map[$r['time_slot_id']][$r['game_type_id']][$r['game_id']] = $r['result_number'];
    }
}

// 1.1 Fetch already calculated winnings from user_winnings table (Source of truth for report)
$winnings_map = [];
$win_query = "SELECT time_slot_id, game_type_id, game_id, amount FROM user_winnings WHERE user_id = '$user_id' AND winning_date = '$date'";
$win_sql = mysqli_query($conn, $win_query);
if ($win_sql) {
    while ($w = mysqli_fetch_assoc($win_sql)) {
        // Map winning total per batch (Slot + Type + Game)
        $winnings_map[$w['time_slot_id']][$w['game_type_id']][$w['game_id']] = (double) $w['amount'];
    }
}

// 2. Fetch tickets
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
        // We show the Double result (Game 3) for the footer display
        $batch_double_res = isset($results_map[$ts_id][$gt_id][3]) ? $results_map[$ts_id][$gt_id][3] : null;

        // Sum winnings for this batch from user_winnings table first
        $batch_win_total = 0;
        if (isset($winnings_map[$ts_id][$gt_id])) {
            foreach ($winnings_map[$ts_id][$gt_id] as $game_total) {
                $batch_win_total += $game_total;
            }
        }

        // Use game_type_code directly from DB (fetched via LEFT JOIN game_types)
        $gt_code = isset($row['game_type_code']) ? $row['game_type_code'] : '??';

        $grouped_data[$batch_key] = [
            'gifteventcode' => $event_code,
            'draw_time' => $draw_time,
            'purchase_date' => $purchase_time,
            'game_type_code' => $gt_code,
            'game_type_id' => $gt_id,
            'winning_number' => $batch_double_res, // For footer display
            'batch_winning_amount' => $batch_win_total, // Use total from user_winnings
            'is_cancelled' => false,
            'cancel_amount' => 0,
            'tickets' => [],
            'winnings_source' => 'table' // Mark that we initialized from table
        ];
    }

    if ($row['status'] == 0) {
        $grouped_data[$batch_key]['is_cancelled'] = true;
        // Use cancelled_at if available, otherwise fallback to purchase_date
        $grouped_data[$batch_key]['cancel_time'] = (!empty($row['cancelled_at'])) ? $row['cancelled_at'] : $row['purchase_date'];
    }

    $n = $row['selected_number'];
    $tickets_to_add = [];
    $qty = (int) $row['qty'];

    $game_id = $row['game_id'];
    if ($game_id == 2) {
        // Side B (Units digit) - Expanded as requested
        $single_res = isset($results_map[$ts_id][$gt_id][2]) ? $results_map[$ts_id][$gt_id][2] : null;
        $double_res = isset($results_map[$ts_id][$gt_id][3]) ? $results_map[$ts_id][$gt_id][3] : null;

        $part_amount = $row['amount'] / 10;

        for ($i = 0; $i <= 9; $i++) {
            $num = $i . $n;
            $win_amt = 0;

            $has_won = false;
            if ($single_res !== null && (int) $single_res === (int) $n) {
                $has_won = true;
            } else if ($double_res !== null) {
                $double_int = (int) $double_res;
                if (($double_int % 10) === (int) $n) {
                    $has_won = true;
                }
            }

            if ($has_won) {
                $win_amt = $qty * 10;
            }

            $tickets_to_add[] = [
                'number' => $num,
                'qty' => $row['qty'],
                'amount' => $part_amount,
                'rate' => $row['rate'],
                'game_id' => $row['game_id'],
                'win_amount' => $win_amt
            ];

            if (empty($winnings_map[$ts_id][$gt_id])) {
                $grouped_data[$batch_key]['batch_winning_amount'] += $win_amt;
            }
        }
    } else if ($game_id == 1) {
        // Side A (Tens digit) - Normal as requested
        $display_number = $n . "0-" . $n . "9";
        $win_amt = 0;
        $has_won = false;

        $single_res = isset($results_map[$ts_id][$gt_id][1]) ? $results_map[$ts_id][$gt_id][1] : null;
        $double_res = isset($results_map[$ts_id][$gt_id][3]) ? $results_map[$ts_id][$gt_id][3] : null;

        if ($single_res !== null && (int) $single_res === (int) $n) {
            $has_won = true;
        } else if ($double_res !== null) {
            $double_int = (int) $double_res;
            if ((int) floor($double_int / 10) === (int) $n) {
                $has_won = true;
            }
        }

        if ($has_won) {
            $win_amt = $qty * 100;
        }

        $tickets_to_add[] = [
            'number' => $display_number,
            'qty' => $row['qty'] * 10,
            'amount' => $row['amount'],
            'rate' => $row['rate'],
            'game_id' => $row['game_id'],
            'win_amount' => $win_amt
        ];

        if (empty($winnings_map[$ts_id][$gt_id])) {
            $grouped_data[$batch_key]['batch_winning_amount'] += $win_amt;
        }
    } else if ($game_id == 3) {
        // Double Game
        $double_res = isset($results_map[$ts_id][$gt_id][3]) ? $results_map[$ts_id][$gt_id][3] : null;
        $win_amt = 0;
        if ($double_res !== null && (int) $double_res === (int) $n) {
            $win_amt = $qty * 900;
        }

        $tickets_to_add[] = [
            'number' => $n,
            'qty' => $row['qty'],
            'amount' => $row['amount'],
            'rate' => $row['rate'],
            'game_id' => $row['game_id'],
            'win_amount' => $win_amt
        ];

        if (empty($winnings_map[$ts_id][$gt_id])) {
            $grouped_data[$batch_key]['batch_winning_amount'] += $win_amt;
        }
    }

    foreach ($tickets_to_add as $t) {
        $t['status'] = $row['status'];
        $grouped_data[$batch_key]['tickets'][] = $t;
        if ($row['status'] == 0) {
            $grouped_data[$batch_key]['cancel_amount'] += $t['amount'];
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