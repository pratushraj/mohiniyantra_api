<?php

require_once('./connection.php');
date_default_timezone_set('Asia/Kolkata');
$currentTime = date('Y-m-d H:i:s');
$today = date('Y-m-d');
// Current Running Game
$currentGameTimeSlotRes = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT * FROM time_slots WHERE time > '$currentTime' LIMIT 1;"));

if (isset($currentGameTimeSlotRes) && !empty($currentGameTimeSlotRes)) {
    $gameDetails['end_time']       = $currentGameTimeSlotRes['time'];
    $allowedTimeStamp = date('Y-m-d H:i:s', strtotime($gameDetails['end_time'] . ' -1 minute'));
} else {
    $allowedTimeStamp = date('Y-m-d H:i:s', strtotime('08:29:00' . ' +1 day'));
}
// Current running game

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // Method not allowed
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data || !isset($data['userId']) || !isset($data['tickets'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => false, 'msg' => 'Invalid data 1']);
    exit;
}


$insertQuery = "INSERT INTO tickets (user_id, game_id, game_type_id, time_slot_id, ticket_date, selected_number, purchase_date, qty, rate, amount ) VALUES ";
$ticketValues = [];
// ticket details fetching
$ticketPriceSql = mysqli_query($conn, "SELECT game_id, ticket_price FROM games");
$ticketPriceRes = [];
while ($row = mysqli_fetch_assoc($ticketPriceSql)) {
    $ticketPriceRes[$row['game_id']] = $row['ticket_price'];
}
// preparing data to insert
$totalPurchaseAmount = 0;
foreach ($data['tickets'] as $ticket) {
    if (!isset($ticket['game_id'], $ticket['game_type_id'], $ticket['ticket_date'], $ticket['time_slot_id'], $ticket['number'], $ticket['qty'])) {
        http_response_code(400);
        echo json_encode(['status' => false, 'msg' => 'Invalid data 2']);
        exit;
    }
    if ($currentTime > $allowedTimeStamp) {
        http_response_code(400);
        echo json_encode(['status' => false, 'msg' => 'Invalid data 3']);
        exit;
    }

    $user_id = $data['userId'];
    $game_id = $ticket['game_id'];
    $game_type_id = $ticket['game_type_id'];
    $time_slot_id_arr = $ticket['time_slot_id'];

    $ticketDate = $ticket['ticket_date'];
    $selected_number = $ticket['number'];
    $ticketQty = $ticket['qty'];
    $rate = $ticketPriceRes[$ticket['game_id']];
    if($game_id == 1 || $game_id == 2){
        $amount = $ticket['qty'] * ($ticketPriceRes[$ticket['game_id']] * 10);
    } else {
        $amount = $ticket['qty'] * $ticketPriceRes[$ticket['game_id']];
    }

    foreach ($time_slot_id_arr as $time_slot_id) {
        $ticketValues[] = "('$user_id', '$game_id', '$game_type_id', '$time_slot_id', '$ticketDate', '$selected_number', NOW(), '$ticketQty', '$rate', '$amount')";
    }


    $totalPurchaseAmount += $amount;
}

// wallet balace checking
$userDetailsRes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT wallet_balance FROM users WHERE id = $user_id AND status = 1"));
if (isset($userDetailsRes) && !empty($userDetailsRes)) {
    if ($userDetailsRes['wallet_balance'] < $totalPurchaseAmount) {
        http_response_code(402);
        echo json_encode(['status' => false, 'msg' => 'Insufficient Balance']);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => false, 'msg' => 'Invalid data 4']);
    exit;
}


$insertQuery .= implode(", ", $ticketValues);

if (mysqli_query($conn, $insertQuery)) {
    // update wallet balance
    $userWalletBalanceUpdSql = mysqli_query($conn, "UPDATE users SET wallet_balance = wallet_balance - $totalPurchaseAmount WHERE id = $user_id");


    // Check purchase summary exist or not
    $checkingSql = mysqli_query($conn, "SELECT * FROM purchase_summary WHERE user_id = $user_id AND date = '$today'");
    $wallet_balnce = $userDetailsRes['wallet_balance'];
    if (mysqli_num_rows($checkingSql) > 0) {
        // update
        $purchaseSummarySql = mysqli_query($conn, "
            UPDATE `purchase_summary`
            SET 
                purchase_pts = purchase_pts + $totalPurchaseAmount,
                net_pts = net_pts - $totalPurchaseAmount,
                balance_pts = balance_pts - $totalPurchaseAmount
            WHERE user_id = $user_id AND date = '$today';
        ");
    } else {
        // insert purchase summary
        $netPts = (0 - $totalPurchaseAmount);
        $balance_pts = $wallet_balnce + $netPts;
        $purchaseSummarySql = mysqli_query($conn, "INSERT INTO `purchase_summary`(`user_id`, `date`, `opening_balance`, `purchase_pts`, `gift_pts`, `net_pts`, `balance_pts`) 
        VALUES ($user_id,'$today','$wallet_balnce','$totalPurchaseAmount','0','$netPts','$balance_pts')");
    }


    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Tickets inserted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => false, 'msg' => 'Failed to insert tickets: ' . mysqli_error($conn)]);
}
