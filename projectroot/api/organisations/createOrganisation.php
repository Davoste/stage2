<?php
// api/organisations/createOrganisation.php
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

$organisation = new Organisation($db);

$data = json_decode(file_get_contents("php://input"));

if (empty($data->name)) {
    http_response_code(422);
    echo json_encode(["errors" => [["field" => "name", "message" => "Name is required"]]]);
    return;
}

$organisation->orgId = uniqid();
$organisation->name = $data->name;
$organisation->description = $data->description ?? '';

if ($organisation->create()) {
    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "Organisation created successfully",
        "data" => [
            "orgId" => $organisation->orgId,
            "name" => $organisation->name,
            "description" => $organisation->description
        ]
    ]);
} else {
    http_response_code(400);
    echo json_encode(["status" => "Bad Request", "message" => "Client error", "statusCode" => 400]);
}
?>
