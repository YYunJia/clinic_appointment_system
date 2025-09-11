<?php
require_once __DIR__ . '/../database.php';

class User {
    private $conn;

    public $user_id;
    public $username;
    public $password_hash;
    public $email;
    public $name;
    public $phone_number;
    public $user_type;

    public function __construct($data = null) {
        $this->conn = getDBConnection();
        if ($data) {
            foreach ($data as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    // Getters
    public function getUserId() { return $this->user_id; }
    public function getUsername() { return $this->username; }
    public function getUserType() { return $this->user_type; }
    public function getPasswordHash() { return $this->password_hash; }

    // Convert to array for events
    public function toArray() {
        return [
            'user_id' => $this->user_id,
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'user_type' => $this->user_type
        ];
    }

    // Check if email exists
    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Check if username exists
    public function usernameExists($username) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Create a new user
    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO users (user_id, username, password_hash, email, name, phone_number, user_type)
            VALUES (:user_id, :username, :password_hash, :email, :name, :phone_number, :user_type)
        ");

        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password_hash', $data['password_hash']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':phone_number', $data['phone_number']);
        $stmt->bindParam(':user_type', $data['user_type']);

        return $stmt->execute() ? $data['user_id'] : false;
    }

    // Find user by ID
    public function findById($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new User($row) : null;
    }

    // Find user by email
    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new User($row) : null;
    }

    // Get last user of a type
    public function getLastUserByType($role) {
        $stmt = $this->conn->prepare("
            SELECT user_id FROM users
            WHERE user_type = :role
            ORDER BY user_id DESC
            LIMIT 1
        ");
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update user
    public function update($data) {
        if (!isset($this->user_id)) return false;

        $fields = [];
        foreach ($data as $key => $value) {
            if ($key !== 'user_id') $fields[] = "$key = :$key";
        }

        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }
}
?>



