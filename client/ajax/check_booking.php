<?php
require_once '../../includes/database.php';
session_start();



if (!isset($_SESSION['user_id'])) {
    die(json_encode(['exists' => false]));
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT status 
        FROM bookings 
        WHERE user_id = ? 
        AND status IN ('pending', 'approved') 
        LIMIT 1
    ");
    
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $booking = $result->fetch_assoc();
    
    echo json_encode([
        'exists' => $result->num_rows > 0,
        'status' => $booking ? $booking['status'] : null
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'exists' => false,
        'error' => 'Database error'
    ]);
} 