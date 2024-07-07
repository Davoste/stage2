<?php
// api/users/getUser.php
include_once '../../config/database.php';
include_once '../../models/User.php';
include_once '../../utils/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Get JWT token from Authorization header
$authHeader = getallheaders()['Authorization'] ?? '';
$jwt = str_replace('Bearer ', '', $authHeader);

if (!$jwt) {
    http_response_code(401);
    echo json_encode(["status" => "Unauthorized", "message" => "Access token is missing or invalid"]);
    return;
}

$decoded = decodeJWT($jwt);

if (!$decoded) {
    http_response_code(401);
    echo json_encode(["status" => "Unauthorized", "message" => "Access token is invalid"]);
    return;
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$user->userId = $decoded->data->userId;

if ($user->getUserById()) {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "User retrieved successfully",
        "data" => [
            "userId" => $user->userId,
            "firstName" => $user->firstName,
            "lastName" => $user->lastName,
            "email" => $user->email,
            "phone" => $user->phone
        ]
    ]);
} else {
    http_response_code(404);
    echo json_encode(["status" => "Not Found", "message" => "User not found"]);
}
?>
