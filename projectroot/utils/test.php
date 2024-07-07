<?php
require_once 'jwt.php';

// Generate a unique user ID
$userId = generateUniqueUserId();
echo 'Generated User ID: ' . $userId . '<br>';


// Generate JWT
$jwt = generateJWT($userId);
echo 'Generated JWT: ' . $jwt . '<br>';

// Decode JWT (example)
$decoded = decodeJWT($jwt);
if ($decoded) {
    echo 'Decoded JWT: ';
    print_r($decoded);
} else {
    echo 'Invalid JWT or JWT expired.';
}
?>
