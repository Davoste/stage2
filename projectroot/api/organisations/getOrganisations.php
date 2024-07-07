<?php
// api/organisations/getOrganisations.php
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

$userId = $decoded->data->userId;
$stmt = $organisation->getUserOrganisations($userId);

$organisations = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $organisations[] = $row;
}

http_response_code(200);
echo json_encode([
    "status" => "success",
    "message" => "Organisations retrieved successfully",
    "data" => [
        "organisations" => $organisations
    ]
]);
?>
