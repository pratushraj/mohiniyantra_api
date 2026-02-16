<?php
require_once('./connection.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // Method not allowed
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validate the input data
if (!$data || !isset($data['from_date']) || !isset($data['to_date']) || !isset($data['user_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => false, 'msg' => 'Invalid data']);
    exit;
}

function parseDate($inputDate) {
    $dateFormats = ['d/m/Y', 'd-m-Y']; 
    foreach ($dateFormats as $format) {
        $dateObject = DateTime::createFromFormat($format, $inputDate);
        if ($dateObject && $dateObject->format($format) === $inputDate) {
            return $dateObject->format('Y-m-d'); 
        }
    }
    return null;
}

// Handle 'from_date'
$from_date = isset($data['from_date']) ? parseDate($data['from_date']) : null;

// Handle 'to_date'
$to_date = isset($data['to_date']) ? parseDate($data['to_date']) : null;

// $dateObject = DateTime::createFromFormat('d/m/Y', $data['from_date']);
// if ($dateObject) {
//     $from_date =  $dateObject->format('Y-m-d');
// } else {
//     // Handle the error if the date is invalid
//     $from_date =  null;
// }

// $dateObjectTo = DateTime::createFromFormat('d/m/Y', $data['to_date']);
// if ($dateObjectTo) {
//     $to_date =  $dateObjectTo->format('Y-m-d');
// } else {
//     // Handle the error if the date is invalid
//     $to_date =  null;
// }


// $from_date = date('Y-m-d', strtotime($data['from_date']));
// $to_date   = date('Y-m-d', strtotime($data['to_date']));
$user_id   = $data['user_id'];
// echo "SELECT * FROM `purchase_summary` WHERE user_id = $user_id AND date BETWEEN '".$from_date."' AND '".$to_date."'";
// die;

$purchaseSummarySql = mysqli_query($conn,"SELECT * FROM `purchase_summary` WHERE user_id = $user_id AND date BETWEEN '".$from_date."' AND '".$to_date."'");

if( mysqli_num_rows($purchaseSummarySql) > 0 ) {
    $summary = [];
    while($purchaseSummaryRes = mysqli_fetch_assoc($purchaseSummarySql)) {
        $summary[] = $purchaseSummaryRes;
    }
    http_response_code(200);
    echo json_encode(['status' => true, 'msg' => 'Success', 'data' => $summary]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['status' => false, 'msg' => 'No data found']);
    exit;
}

;


?>