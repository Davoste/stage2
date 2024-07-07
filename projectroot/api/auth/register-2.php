<?php
// api/auth/register.php
include_once '../../config/database.php';
include_once '../../models/User.php';
include_once '../../models/Organisation.php';
include_once '../../utils/jwt.php';
include_once '../../utils/validation.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$organisation = new Organisation($db);

$data = json_decode(file_get_contents("php://input"));

$errors = validateUser($data);

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["errors" => $errors]);
    return;
}

$user->userId = uniqid();
$user->firstName = $data->firstName;
$user->lastName = $data->lastName;
$user->email = $data->email;
$user->password = $data->password;
$user->phone = $data->phone;

if ($user->create()) {
    $organisation->orgId = uniqid();
    $organisation->name = $user->firstName . "'s Organisation";
    $organisation->description = "";

    if ($organisation->create()) {
        // Create JWT token
        $jwt = generateJWT($user->userId);

        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Registration successful",
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
        http_response_code(400);
        echo json_encode(["status" => "Bad request", "message" => "Registration unsuccessful", "statusCode" => 400]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "Bad request", "message" => "Registration unsuccessful", "statusCode" => 400]);
}
?>
