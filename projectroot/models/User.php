<?php

session_start();
$userId = $_SESSION['userId'];

// models/User.php
class User {
    private $conn;
    private $table_name = "users";

    public $userId; // Added this property
    public $firstName;
    public $lastName;
    public $email;
    public $password;
    public $phone;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        // Check if userId is set from session
        if (!isset($GLOBALS['userId'])) {
            die('User ID not set from session.');
        }

        $query = "INSERT INTO " . $this->table_name . " (userId, firstName, lastName, email, password, phone) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            die('prepare() failed: ' . htmlspecialchars($this->conn->error));
        }

        // Use parameters from the class properties
        $this->firstName = htmlspecialchars(strip_tags($this->firstName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->phone = htmlspecialchars(strip_tags($this->phone));

        // Bind parameters
        $stmt->bind_param("ssssss", $GLOBALS['userId'], $this->firstName, $this->lastName, $this->email, $this->password, $this->phone);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function emailExists() {
        // Check if email is set
        if (!isset($this->email)) {
            die('Email not set.');
        }

        $query = "SELECT userId, firstName, lastName, password, phone FROM " . $this->table_name . " WHERE email = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('prepare() failed: ' . htmlspecialchars($this->conn->error));
        }

        // Bind email parameter
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->userId = $row['userId'];
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->password = $row['password'];
            $this->phone = $row['phone'];
            return true;
        } else {
            return false;
        }
    }

    public function getUserById() {
        // Check if userId is set
        if (!isset($this->userId)) {
            die('User ID not set.');
        }

        $query = "SELECT userId, firstName, lastName, email, phone FROM " . $this->table_name . " WHERE userId = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('prepare() failed: ' . htmlspecialchars($this->conn->error));
        }

        // Bind userId parameter
        $stmt->bind_param("s", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->userId = $row['userId'];
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            return true;
        } else {
            return false;
        }
    }
}
?>