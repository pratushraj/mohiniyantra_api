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

// Check if cancelled_at column exists, try to auto-add if missing
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM tickets LIKE 'cancelled_at'");
if (mysqli_num_rows($checkCol) == 0) {
    // Attempt auto-add (might fail based on DB user permissions, but worth a try)
    mysqli_query($conn, "ALTER TABLE tickets ADD COLUMN cancelled_at TIMESTAMP NULL DEFAULT NULL");
    $checkCol = mysqli_query($conn, "SHOW COLUMNS FROM tickets LIKE 'cancelled_at'");
}
$hasCol = mysqli_num_rows($checkCol) > 0;

$userId = mysqli_real_escape_string($conn, $data['userId']);
$gameId = isset($data['gameId']) ? mysqli_real_escape_string($conn, $data['gameId']) : null;

// 1. Check Maximum Cancellation Limit (3 batches today)
if ($hasCol) {
    $limitCheckQuery = "SELECT COUNT(DISTINCT purchase_date) as total_cancels 
                        FROM tickets 
                        WHERE user_id = '$userId' AND status = 0 AND DATE(cancelled_at) = CURDATE()";
} else {
    // Fallback if column is still missing
    $limitCheckQuery = "SELECT COUNT(DISTINCT purchase_date) as total_cancels 
                        FROM tickets 
                        WHERE user_id = '$userId' AND status = 0 AND ticket_date = '$today'";
}

$limitCheckSql = mysqli_query($conn, $limitCheckQuery);
$limitRes = mysqli_fetch_assoc($limitCheckSql);

if ($limitRes && $limitRes['total_cancels'] >= 3) {
    http_response_code(400);
    echo json_encode(['status' => false, 'msg' => 'You have reached the maximum cancel limit']);
    exit;
}

// 2. Find the latest purchase_date for active upcoming tickets
$gameIdFilter = $gameId ? "AND game_id = '$gameId'" : "";
$latestPurchaseQuery = "SELECT purchase_date 
                        FROM tickets 
                        WHERE user_id = '$userId' AND status = 1 AND ticket_date >= '$today' AND time_slot_id > '$lastTimeSlot' $gameIdFilter
                        ORDER BY purchase_date DESC LIMIT 1";
$latestSql = mysqli_query($conn, $latestPurchaseQuery);

if (mysqli_num_rows($latestSql) == 0) {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No tickets found to cancel']);
    exit;
}

$latestRes = mysqli_fetch_assoc($latestSql);
$latestDate = $latestRes['purchase_date'];

// 3. Fetch tickets for this latest batch to calculate refund
$totalRefund = 0;
$fetchSql = mysqli_query($conn, "SELECT amount FROM tickets WHERE user_id = '$userId' AND purchase_date = '$latestDate' AND status = 1");
while ($row = mysqli_fetch_assoc($fetchSql)) {
    $totalRefund += $row['amount'];
}

// 4. Update Wallet
mysqli_query($conn, "UPDATE users SET wallet_balance = wallet_balance + $totalRefund WHERE id = '$userId'");

// 5. Update Purchase Summary
mysqli_query($conn, "UPDATE purchase_summary 
                     SET purchase_pts = purchase_pts - $totalRefund, 
                         net_pts = net_pts + $totalRefund, 
                         balance_pts = balance_pts + $totalRefund 
                     WHERE user_id = '$userId' AND date = '$today'");

// 6. Update Ticket Status

if ($hasCol) {
    $cancelQueryStr = "UPDATE tickets SET status = 0, cancelled_at = NOW() WHERE user_id = ? AND purchase_date = ?";
} else {
    $cancelQueryStr = "UPDATE tickets SET status = 0 WHERE user_id = ? AND purchase_date = ?";
}

$stmt = $conn->prepare($cancelQueryStr);
$stmt->bind_param("ss", $userId, $latestDate);

if ($stmt->execute()) {
    echo json_encode(['status' => true, 'msg' => 'Latest ticket batch cancelled. Points ' . $totalRefund . ' refunded.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => false, 'msg' => 'Failed to cancel tickets']);
}

// Close the statement and database connection
$stmt->close();
mysqli_close($conn);
?>