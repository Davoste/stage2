<?php
$userId = $_SESSION['userId'];
// models/Organisation.php
class Organisation {
    private $conn;
    private $table_name = "organisations";

    public $orgId;
    public $name;
    public $description;
   

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (orgId, name, description) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            die('prepare() failed: ' . htmlspecialchars($this->conn->error));
        }

        $this->orgId;
        $this->name ;
        $this->description ;

       // Bind parameters
       $stmt->bind_param("sss", $this->orgId, $this->name, $this->description);


        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

  
    public function addUserToOrganisation($userId) {
        $query = "INSERT INTO user_organisations (orgId, userId) VALUES (?, ?)";
    
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('prepare() failed: ' . htmlspecialchars($this->conn->error));
        }
        $this->orgId = htmlspecialchars(strip_tags($this->orgId));

        // Bind parameters
       $stmt->bind_param("ss", $this->orgId, $GLOBALS['userId']);
    
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }
    
    public function getUserOrganisations($userId) {
        $query = "SELECT orgId, name, description FROM organisations WHERE orgId IN (SELECT orgId FROM user_organisations WHERE userId = :userId)";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":userId", $userId);
        $stmt->execute();
    
        return $stmt;
    }
    
    public function getOrganisationById() {
        $query = "SELECT orgId, name, description FROM " . $this->table_name . " WHERE orgId = ? LIMIT 1";
    
        $stmt = $this->conn->prepare($query);
    
        if ($stmt === false) {
            throw new Exception('prepare() failed: ' . htmlspecialchars($this->conn->error));
        }
    
        $stmt->bind_param("i", $this->orgId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $num = $result->num_rows;
    
        if ($num > 0) {
            $row = $result->fetch_assoc();
            $this->orgId = $row['orgId'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            return true;
        }
    
        return false;
    }
    
}
?>
