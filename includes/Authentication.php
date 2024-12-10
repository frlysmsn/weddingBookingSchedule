<?php
require_once 'db_connection.php';

class Authentication {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'strength' => $this->calculatePasswordStrength($password)
        ];
    }

    private function calculatePasswordStrength($password) {
        $strength = 0;
        
        if (strlen($password) >= 8) $strength += 25;
        if (preg_match('/[A-Z]/', $password)) $strength += 25;
        if (preg_match('/[0-9]/', $password)) $strength += 25;
        if (preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) $strength += 25;
        
        return $strength;
    }

    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function loginAdmin($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Admin login error: " . $e->getMessage());
            return false;
        }
    }

    public function register($name, $email, $password) {
        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, role) 
                VALUES (?, ?, ?, 'client')
            ");
            $stmt->execute([$name, $email, $hashedPassword]);
            
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
} 