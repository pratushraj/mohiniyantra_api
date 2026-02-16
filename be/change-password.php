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
if (!$data || !isset($data['old_password']) || !isset($data['new_password']) || !isset($data['user_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => false, 'msg' => 'Invalid data']);
    exit;
}

// Sanitize input data
$oldPassword = mysqli_real_escape_string($conn, $data['old_password']);
$newPassword = mysqli_real_escape_string($conn, $data['new_password']);
$user_id = mysqli_real_escape_string($conn, $data['user_id']);

// Retrieve the current hashed password from the database
$query = "SELECT password FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    http_response_code(404); // Not Found
    echo json_encode(['status' => false, 'msg' => 'User not found']);
    exit;
}

$row = $result->fetch_assoc();
$hashedPassword = $row['password'];

// Verify the old password
if (!password_verify($oldPassword, $hashedPassword)) {
    http_response_code(403); // Forbidden
    echo json_encode(['status' => false, 'msg' => 'Old password is incorrect']);
    exit;
}

// Update the password in the database
$newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$updateQuery = "UPDATE users SET password = ? WHERE id = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("ss", $newHashedPassword, $user_id);

if ($updateStmt->execute()) {
    echo json_encode(['status' => true, 'msg' => 'Password updated successfully']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => false, 'msg' => 'Failed to update password']);
}

$stmt->close();
$updateStmt->close();
mysqli_close($conn); // Close the DB connection
