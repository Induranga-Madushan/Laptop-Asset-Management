<?php
// database.php - Database connection configuration
class Database {
    private $host = "localhost";
    private $db_name = "laptop_management";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        return $this->conn;
    }
}

// Laptop.php - Laptop class for managing laptop operations
class Laptop {
    private $conn;
    private $table_name = "laptops";

    // Laptop properties
    public $id;
    public $serial_number;
    public $model;
    public $manufacturer;
    public $status;
    public $assigned_to;
    public $purchase_date;
    public $last_maintenance;
    public $specs;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new laptop record
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (serial_number, model, manufacturer, status, assigned_to, 
                 purchase_date, last_maintenance, specs)
                VALUES
                (:serial_number, :model, :manufacturer, :status, :assigned_to,
                 :purchase_date, :last_maintenance, :specs)";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->serial_number = htmlspecialchars(strip_tags($this->serial_number));
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->manufacturer = htmlspecialchars(strip_tags($this->manufacturer));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->assigned_to = htmlspecialchars(strip_tags($this->assigned_to));
        $this->specs = htmlspecialchars(strip_tags($this->specs));

        // Bind parameters
        $stmt->bindParam(":serial_number", $this->serial_number);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":manufacturer", $this->manufacturer);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":assigned_to", $this->assigned_to);
        $stmt->bindParam(":purchase_date", $this->purchase_date);
        $stmt->bindParam(":last_maintenance", $this->last_maintenance);
        $stmt->bindParam(":specs", $this->specs);

        return $stmt->execute();
    }

    // Read all laptops
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single laptop
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->serial_number = $row['serial_number'];
            $this->model = $row['model'];
            $this->manufacturer = $row['manufacturer'];
            $this->status = $row['status'];
            $this->assigned_to = $row['assigned_to'];
            $this->purchase_date = $row['purchase_date'];
            $this->last_maintenance = $row['last_maintenance'];
            $this->specs = $row['specs'];
            return true;
        }
        return false;
    }

    // Update laptop record
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    serial_number = :serial_number,
                    model = :model,
                    manufacturer = :manufacturer,
                    status = :status,
                    assigned_to = :assigned_to,
                    purchase_date = :purchase_date,
                    last_maintenance = :last_maintenance,
                    specs = :specs
                WHERE
                    id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->serial_number = htmlspecialchars(strip_tags($this->serial_number));
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->manufacturer = htmlspecialchars(strip_tags($this->manufacturer));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->assigned_to = htmlspecialchars(strip_tags($this->assigned_to));
        $this->specs = htmlspecialchars(strip_tags($this->specs));

        // Bind parameters
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":serial_number", $this->serial_number);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":manufacturer", $this->manufacturer);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":assigned_to", $this->assigned_to);
        $stmt->bindParam(":purchase_date", $this->purchase_date);
        $stmt->bindParam(":last_maintenance", $this->last_maintenance);
        $stmt->bindParam(":specs", $this->specs);

        return $stmt->execute();
    }

    // Delete laptop record
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    // Search laptops
    public function search($keywords) {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE 
                    serial_number LIKE ? OR 
                    model LIKE ? OR 
                    manufacturer LIKE ? OR 
                    assigned_to LIKE ?
                ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);

        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $keywords);

        $stmt->execute();
        return $stmt;
    }
}