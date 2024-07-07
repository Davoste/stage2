<?php
// config/database.php
class Database {
    private $host = 'localhost';
    private $db_name = 'users';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Set charset
        $this->conn->set_charset("utf8");

        return $this->conn;
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

?>
