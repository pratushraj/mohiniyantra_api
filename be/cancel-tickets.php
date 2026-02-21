<?php
require_once('./connection.php');
date_default_timezone_set('Asia/Kolkata');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$today = date('Y-m-d');
$timeNow = date('H:i:s');

if (strtotime($timeNow) >= strtotime('22:30:00')) {
    $today = date('Y-m-d', strtotime("+1 day"));
}

$currentGameTimeSlotRes = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT * FROM time_slots WHERE time > '$timeNow' LIMIT 1;"));

if (isset($currentGameTimeSlotRes) && !empty($currentGameTimeSlotRes)) {
    $timeSlotNow = $currentGameTimeSlotRes['time_slot_id'];
} else {
    $timeSlotNow = 1;
}

$lastTimeSlot = $timeSlotNow - 1;

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // Method not allowed
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data || !isset($data['userId'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => false, 'msg' => 'Invalid data']);
    exit;
}

$userId = mysqli_real_escape_string($conn, $data['userId']);

// Fetch all tickets
$totalSingleATicketCount = 0;
$totalSingleBTicketCount = 0;
$totalDoubleTicketCount = 0;

$totalSingleATicketCountPrice = 0;
$totalSingleBTicketCountPrice = 0;
$totalDoubleTicketCountPrice = 0;

$fetchSql = mysqli_query($conn, "SELECT * FROM tickets WHERE user_id = $userId AND `status` = 1 AND ticket_date >= '$today' AND time_slot_id > '$lastTimeSlot'");
while ($fetchRes = mysqli_fetch_assoc($fetchSql)) {
    $gameId = $fetchRes['game_id'];
    $ticketCount = $fetchRes['qty'];
    $ticketPrice = $fetchRes['amount'];
    if ($gameId == 1) {
        $totalSingleATicketCount += $ticketCount;
        $totalSingleATicketCountPrice += $ticketPrice;
    } else if ($gameId == 2) {
        $totalSingleBTicketCount += $ticketCount;
        $totalSingleBTicketCountPrice += $ticketPrice;
    } else if ($gameId == 3) {
        $totalDoubleTicketCount += $ticketCount;
        $totalDoubleTicketCountPrice += $ticketPrice;
    }
}

$totalTicketPrice = $totalSingleATicketCountPrice + $totalSingleBTicketCountPrice + $totalDoubleTicketCountPrice;
// Wallet Balance Update
$userWalletBalanceUpdSql = mysqli_query($conn, "UPDATE users SET wallet_balance = wallet_balance + $totalTicketPrice WHERE id = $userId");


// Purchase Summary Update
$purchaseSummarySql = mysqli_query($conn, "
            UPDATE `purchase_summary`
            SET 
                purchase_pts = purchase_pts - $totalTicketPrice,
                net_pts = net_pts + $totalTicketPrice,
                balance_pts = balance_pts + $totalTicketPrice
            WHERE user_id = $userId AND date = '$today';
        ");


// Update tickets table - try to set cancelled_at if column exists
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM tickets LIKE 'cancelled_at'");
$hasCol = mysqli_num_rows($checkCol) > 0;

if ($hasCol) {
    $query = "UPDATE tickets SET `status` = 0, `cancelled_at` = NOW() WHERE user_id = ? AND ticket_date >= ? AND time_slot_id > ?";
} else {
    $query = "UPDATE tickets SET `status` = 0 WHERE user_id = ? AND ticket_date >= ? AND time_slot_id > ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $userId, $today, $lastTimeSlot);

if ($stmt->execute()) {
    echo json_encode(['status' => true, 'msg' => 'Tickets cancelled Points ' . $totalTicketPrice . ' refunded to wallet']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => false, 'msg' => 'Failed to cancel upcoming tickets']);
}

// Close the statement and database connection
$stmt->close();
mysqli_close($conn);
?>