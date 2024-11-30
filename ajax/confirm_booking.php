<?php
session_start();
require_once '../../includes/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $user_id = $_SESSION['user_id'];
    $bride_name = $_POST['bride_name'];
    $bride_dob = $_POST['bride_dob'];
    // ... other fields ...
    
    $sql = "INSERT INTO bookings (
        user_id, bride_name, bride_dob, /* ... other fields ... */
    ) VALUES (?, ?, ?, /* ... other placeholders ... */)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss...", // add 'i' for user_id
        $_SESSION['user_id'], // add user_id as first parameter
        $bride_name,
        $bride_dob,
        // ... other parameters ...
    );
    
    $stmt->execute();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Booking submitted successfully.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}