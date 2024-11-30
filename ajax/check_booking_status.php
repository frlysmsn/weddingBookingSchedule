<?php
session_start();
require_once '../../includes/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $user_id = $_SESSION['user_id'];
    
    // Check for pending or approved bookings
    $sql = "SELECT status FROM bookings WHERE user_id = ? AND status IN ('pending', 'waiting_for_confirmation')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        echo json_encode([
            'has_booking' => true,
            'status' => $booking['status']
        ]);
    } else {
        echo json_encode([
            'has_booking' => false,
            'status' => null
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 