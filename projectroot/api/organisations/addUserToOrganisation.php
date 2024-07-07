<?php
// api/organisations/addUserToOrganisation.php
include_once '../../config/database.php';
include_once '../../models/Organisation.php';
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

$data = json_decode(file_get_contents("php://input"));

if (empty($data->userId) || empty($data->orgId)) {
    http_response_code(422);
    echo json_encode(["errors" => [["field" => "userId/orgId", "message" => "User ID and Organisation ID are required"]]]);
    return;
}

$organisation = new Organisation($db);
$organisation->orgId = $data->orgId;

if ($organisation->addUserToOrganisation($data->userId)) {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "User added to organisation successfully"
    ]);
} else {
    http_response_code(400);
    echo json_encode(["status" => "Bad Request", "message" => "Client error", "statusCode" => 400]);
}
?>
