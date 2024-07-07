<?php
// utils/validation.php
function validateUser($data) {
    $errors = [];

    if (empty($data->firstName)) {
        $errors[] = ["field" => "firstName", "message" => "First name is required"];
    }

    if (empty($data->lastName)) {
        $errors[] = ["field" => "lastName", "message" => "Last name is required"];
    }

    if (empty($data->email)) {
        $errors[] = ["field" => "email", "message" => "Email is required"];
    } elseif (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = ["field" => "email", "message" => "Invalid email format"];
    }

    if (empty($data->password)) {
        $errors[] = ["field" => "password", "message" => "Password is required"];
    }

    return $errors;
}
?>
