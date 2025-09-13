<?php

require_once __DIR__ . '/../database.php';

class User {
    protected $conn;
    protected $table = 'users';

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
            $this->fill($data);
        }
    }

    /** Fill object properties from array */
    public function fill(array $data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /** Convert object to array */
    public function toArray() {
        return [
            'user_id' => $this->user_id,
            'username' => $this->username,
            'password_hash' => $this->password_hash,
            'email' => $this->email,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'user_type' => $this->user_type
        ];
    }

    /** Getters for compatibility with AuthService */
    public function getUserId() {
        return $this->user_id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getUserType() {
        return $this->user_type;
    }

    public function getPasswordHash() {
        return $this->password_hash;
    }

    /** Save (insert or update) */
    public function save() {
        if ($this->user_id) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    /** Create new record (public for AuthService) */
    public function create() {
        $fields = array_keys($this->toArray());
        $placeholders = array_map(fn($f) => ":$f", $fields);

        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ")
                VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);

        foreach ($this->toArray() as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        if ($stmt->execute()) {
            return $this->user_id;
        }
        return false;
    }

    /** Update existing record (public for AuthService) */
    public function update() {
        $fields = [];
        foreach ($this->toArray() as $key => $value) {
            if ($key !== 'user_id') $fields[] = "$key = :$key";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);

        foreach ($this->toArray() as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    /** Find by primary key */
    public static function find($user_id) {
        $instance = new static();
        $stmt = $instance->conn->prepare("SELECT * FROM {$instance->table} WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new static($data) : null;
    }

    /** Find by email */
    public static function findByEmail($email) {
        $instance = new static();
        $stmt = $instance->conn->prepare("SELECT * FROM {$instance->table} WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new static($data) : null;
    }

    /** Check if email exists */
    public static function emailExists($email) {
        $instance = new static();
        $stmt = $instance->conn->prepare("SELECT COUNT(*) FROM {$instance->table} WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /** Check if username exists */
    public static function usernameExists($username) {
        $instance = new static();
        $stmt = $instance->conn->prepare("SELECT COUNT(*) FROM {$instance->table} WHERE username = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /** Get last user by type */
    public static function getLastUserByType($role) {
        $instance = new static();
        $stmt = $instance->conn->prepare("
            SELECT user_id FROM {$instance->table}
            WHERE user_type = :role
            ORDER BY user_id DESC
            LIMIT 1
        ");
        $stmt->bindValue(':role', $role);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>



