<?php
require_once('./connection.php');
date_default_timezone_set('Asia/Kolkata');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405); // Method not allowed
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

$ticketPriceSql = mysqli_query($conn, "SELECT game_id, game_name, ticket_price FROM games;");

if( mysqli_num_rows($ticketPriceSql) > 0 ) {
    $price = [];
    while( $ticketPriceRes = mysqli_fetch_assoc($ticketPriceSql) ) {
        $price[] = $ticketPriceRes;
    }
    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $price]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No data found']);
    exit;
}
