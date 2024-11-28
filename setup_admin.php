<?php
require_once 'includes/config.php';
require_once 'includes/db_connection.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if admin exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = 'admin@admin.com'");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        // Create admin account if it doesn't exist
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'System Administrator',
            'admin@admin.com',
            password_hash('admin', PASSWORD_DEFAULT),
            'admin'
        ]);
        
        echo "Admin account created successfully!";
    } else {
        echo "Admin account already exists!";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} 