<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// // Database configuration
// $servername = "localhost"; // Database server name
// $username = "root";         // Database username
// $password = "";             // Database password
// $dbname = "rajarani_gaming";  // Database name

// Production configuration
$servername = "localhost"; // Database server name
$username = "u547026376_gwwin";         // Database username
$password = "32dS1CIEn@";             // Database password
$dbname = "u547026376_gwwin";  // Database name


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // echo "Connected successfully";
}


?>