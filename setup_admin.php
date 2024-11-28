<?php
require_once 'includes/config.php';
require_once 'includes/db_connection.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Delete existing admin if exists
    $stmt = $db->prepare("DELETE FROM users WHERE email = ?");
    $stmt->execute(['admin@admin.com']);
    
    // Create new admin
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("
        INSERT INTO users (name, email, password, role) 
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([
        'Admin',
        'admin@admin.com',
        $hashed_password,
        'admin'
    ]);
    
    echo "Admin account created successfully!<br>";
    echo "Email: admin@admin.com<br>";
    echo "Password: admin123";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} 