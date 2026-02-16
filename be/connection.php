<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// // Database configuration
// $servername = "localhost"; // Database server name
// $username = "root";         // Database username
// $password = "";             // Database password
// $dbname = "rajarani_gaming";  // Database name

// Production configuration
$servername = "localhost"; // Database server name
$username = "root";         // Database username
$password = "Pratush@8804";             // Database password
$dbname = "rajrani";  // Database name


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // echo "Connected successfully";
}


?>