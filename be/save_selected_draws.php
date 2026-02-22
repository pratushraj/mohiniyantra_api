<?php
require_once('./connection.php');
date_default_timezone_set('Asia/Kolkata');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['userId']) || !isset($data['slotIds']) || !isset($data['date'])) {
    echo json_encode(['status' => false, 'msg' => 'Missing required fields']);
    exit;
}

$userId = $data['userId'];
$slotIds = $data['slotIds'];
$drawDate = $data['date'];

// Create table if it doesn't exist
$createTableSql = "CREATE TABLE IF NOT EXISTS user_selected_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    time_slot_id INT NOT NULL,
    draw_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $createTableSql);

// First, clear existing selections for this user on this date (optional, depends on requirement)
// The user said "insert krwana hai", so maybe they want to append or replace. 
// Usually, for "Selected draws", we replace the old list.
$deleteSql = "DELETE FROM user_selected_slots WHERE user_id = '$userId' AND draw_date = '$drawDate'";
mysqli_query($conn, $deleteSql);

$successCount = 0;
foreach ($slotIds as $slotId) {
    $insertSql = "INSERT INTO user_selected_slots (user_id, time_slot_id, draw_date) VALUES ('$userId', '$slotId', '$drawDate')";
    if (mysqli_query($conn, $insertSql)) {
        $successCount++;
    }
}

if ($successCount > 0) {
    echo json_encode(['status' => true, 'msg' => "Successfully selected $successCount draws"]);
} else {
    echo json_encode(['status' => false, 'msg' => 'Failed to save selection']);
}
?>