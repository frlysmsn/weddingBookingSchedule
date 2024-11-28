<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $booking_id = $_POST['booking_id'];
    
    // Verify booking belongs to user
    $stmt = $db->prepare("
        SELECT status 
        FROM bookings 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$booking_id, $_SESSION['user_id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        throw new Exception('Booking not found or unauthorized');
    }
    
    if ($booking['status'] === 'cancelled') {
        throw new Exception('Booking is already cancelled');
    }
    
    // Update booking status
    $stmt = $db->prepare("
        UPDATE bookings 
        SET status = 'cancelled', 
            cancelled_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$booking_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking cancelled successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 