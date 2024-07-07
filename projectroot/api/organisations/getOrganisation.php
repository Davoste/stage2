<?php
// api/organisations/getOrganisation.php
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

$orgId = $_GET['orgId'] ?? '';

if (empty($orgId)) {
    http_response_code(422);
    echo json_encode(["errors" => [["field" => "orgId", "message" => "Organisation ID is required"]]]);
    return;
}

$organisation->orgId = $orgId;

if ($organisation->getOrganisationById()) {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Organisation retrieved successfully",
        "data" => [
            "orgId" => $organisation->orgId,
            "name" => $organisation->name,
            "description" => $organisation->description
        ]
    ]);
} else {
    http_response_code(404);
    echo json_encode(["status" => "Not Found", "message" => "Organisation not found"]);
}
?>
