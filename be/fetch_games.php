<?php
require_once('./connection.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

// Fetch all game types from the database
$sql = "SELECT * FROM game_types";
$result = mysqli_query($conn, $sql);

if ($result) {
    $gameTypes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $gameTypes[] = [
            'id' => $row['game_type_id'],
            'name' => $row['game_type_name'],
            'code' => $row['game_type_code']
        ];
    }

    http_response_code(200);
    echo json_encode([
        'status' => true,
        'msg' => 'Success',
        'data' => $gameTypes
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'msg' => 'Database error',
        'error' => mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>