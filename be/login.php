<?php
require_once('./connection.php');

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'msg' => 'Method Not Allowed']);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validate the input data
if (!$data || !isset($data['email']) || !isset($data['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => false, 'msg' => 'Invalid data']);
    exit;
}

// Sanitize input data
$inputEmail = mysqli_real_escape_string($conn, $data['email']);
$inputPass = mysqli_real_escape_string($conn, $data['password']);

$cacheCode = $data['cache_code'] ?? null; // Optional cache code for checking if user is already logged in

function generateRandomPassword($length = 32)
{
    // Characters to use in password
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}<>?';
    $charactersLength = strlen($characters);
    $password = '';

    for ($i = 0; $i < $length; $i++) {
        $index = random_int(0, $charactersLength - 1); // cryptographically secure
        $password .= $characters[$index];
    }

    return $password;
}
// 32-character password


// Fetch the user from the database
$sql = "SELECT * FROM users WHERE username = '$inputEmail' AND status = 1";
$result = mysqli_query($conn, $sql);

// Check if user exists
if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    // Verify the password 
    if (password_verify($inputPass, $user['password'])) {

        if ($user['is_logged_in'] && $user['cache'] != $cacheCode) {
            http_response_code(409); // OK
            echo json_encode([
                'status' => false,
                'msg' => 'User already logged in',
                'data' => null
            ]);
        } else {
            $cacheGenerated = generateRandomPassword(32);
            $userId = $user['id'];
            $updateUserSql = mysqli_query($conn, "UPDATE users SET is_logged_in = 1, cache  = '" . $cacheGenerated . "' WHERE id = $userId");
            http_response_code(200); // OK
            echo json_encode([
                'status' => true,
                'msg' => 'Login successful',
                'data' => [
                    'id' => $user['id'],
                    'unique_id' => $user['user_unique_id'],
                    'name' => $user['name'],
                    'wallet_balance' => $user['wallet_balance'],
                    'cache' => $cacheGenerated
                ]
            ]);
        }
    } else {
        // Incorrect password
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => false, 'msg' => 'Incorrect username or password']);
    }
} else {
    // User not found
    http_response_code(404); // Not Found
    echo json_encode(['status' => false, 'msg' => 'Incorrect username or password']);
}

mysqli_close($conn); // Close the DB connection
