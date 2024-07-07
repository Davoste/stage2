<?php
// utils/jwt.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ .'/../vendor/firebase/php-jwt/src/JWT.php';
require_once __DIR__ .'/../vendor/firebase/php-jwt/src/ExpiredException.php';
require_once __DIR__ .'/../vendor/firebase/php-jwt/src/SignatureInvalidException.php';
require_once __DIR__ .'/../vendor/firebase/php-jwt/src/BeforeValidException.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;

function generateUniqueUserId() {
    return uniqid('user_', true); // Generates a unique user ID prefixed with 'user_'
}
// Generate a unique user ID
$userId = generateUniqueUserId();

// Store it in a session (optional)
$_SESSION['userId'] = $userId;


function generateJWT($userId) {
    $secret_key = "your_very_secure_secret_key_for_local_dev";
    $issuer_claim = "http://localhost:8080";
    $audience_claim = "http://localhost:8080";

    $issuedat_claim = time();
    $notbefore_claim = $issuedat_claim + 10;
    $expire_claim = $issuedat_claim + 3600;

    $token = array(
        "iss" => $issuer_claim,
        "aud" => $audience_claim,
        "iat" => $issuedat_claim,
        "nbf" => $notbefore_claim,
        "exp" => $expire_claim,
        "data" => array(
            "userId" => $userId
        )
    );

    $jwt = JWT::encode($token, $secret_key, 'HS256');
    return $jwt;
}

function decodeJWT($jwt) {
    $key = "your_very_secure_secret_key_for_local_dev"; // Replace with your actual secret key

    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        return $decoded;
    } catch (ExpiredException $e) {
        return null;
    } catch (SignatureInvalidException $e) {
        return null;
    } catch (BeforeValidException $e) {
        return null;
    } catch (Exception $e) {
        return null;
    }
}
?>
