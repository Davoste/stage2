<?php
// api/auth/login.php
include_once '../../config/database.php';
include_once '../../models/User.php';
include_once '../../utils/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$user->email = $data->email;
$email_exists = $user->emailExists();

if ($email_exists && password_verify($data->password, $user->password)) {
    $jwt = generateJWT($user->userId);

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "data" => [
            "accessToken" => $jwt,
            "user" => [
                "userId" => $user->userId,
                "firstName" => $user->firstName,
                "lastName" => $user->lastName,
                "email" => $user->email,
                "phone" => $user->phone
            ]
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode(["status" => "Bad request", "message" => "Authentication failed", "statusCode" => 401]);
}
?>
