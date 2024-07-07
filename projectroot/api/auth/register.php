<?php
// api/auth/register.php
include_once '../../config/database.php';
include_once '../../models/User.php';
include_once '../../models/Organisation.php';
include_once '../../utils/jwt.php';
include_once '../../utils/validation.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$userId = $_SESSION['userId'];

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

$user->userId = $userId;
$user->firstName = $data->firstName;
$user->lastName = $data->lastName;
$user->email = $data->email;
$password = $data->password;
$cpassword = $data->cpassword;
$user->phone = $data->phone;

if ($password !== $cpassword) {
    $errors['password'] = "Password and Confirm password do not match!";
}

if (!preg_match("/^\d{10}$/", $user->phone)) {
    $errors['phone'] = "Phone number must contain 10 digits only.";
}

$phone_check = "SELECT * FROM users WHERE phone = ?";
$stmt = $db->prepare($phone_check);
$stmt->bind_param('s', $user->phone);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $errors['existingPhone'] = "This phone number is already registered to another account!";
}

$uppercase = preg_match('@[A-Z]@', $password);
$lowercase = preg_match('@[a-z]@', $password);
$number = preg_match('@[0-9]@', $password);
$specialChar = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);

if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChar) {
    $errors['password'] = "Password should be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
}

if (!empty($errors)) {
    http_response_code(422);
    
    echo json_encode([
        "status" => "Error",
        "errors" => $errors
    ]);
    return;
}

$user->password = password_hash($password, PASSWORD_BCRYPT); // Hash the password

if ($user->create()) {
    $organisation->orgId = $userId;
    $organisation->name = $user->firstName . "'s Organisation";
    $organisation->description = "This is " . $user->firstName . "'s Organisation";

    if ($organisation->create()) {
        if ($organisation->addUserToOrganisation($userId)) {
            $jwt = generateJWT($user->userId);

            if ($organisation->getOrganisationById()) {
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
                        ],
                        "organisation" => [
                            "orgId" => $organisation->orgId,
                            "name" => $organisation->name,
                            "description" => $organisation->description
                        ]
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to fetch organization details after creation"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to add user to organisation"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "Bad request", "message" => "Failed to create organisation"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "Bad request", "message" => "Registration unsuccessful"]);
}
?>
