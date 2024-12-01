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
    
    $original_booking_id = $_POST['original_booking_id'];
    $wedding_date = $_POST['wedding_date'];
    $preferred_time = $_POST['preferred_time'];
    
    // Start transaction
    $db->beginTransaction();
    
    // Get original booking details
    $stmt = $db->prepare("
        SELECT * FROM bookings 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$original_booking_id, $_SESSION['user_id']]);
    $original_booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$original_booking) {
        throw new Exception('Original booking not found');
    }
    
    // Check if selected date and time is available
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM bookings 
        WHERE wedding_date = ? 
        AND preferred_time = ? 
        AND status != 'cancelled'
    ");
    $stmt->execute([$wedding_date, $preferred_time]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Selected date and time is already booked');
    }
    
    // Create new booking
    $stmt = $db->prepare("
        INSERT INTO bookings (
            user_id, wedding_date, preferred_time, 
            groom_name, bride_name, contact_number, 
            email, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $wedding_date,
        $preferred_time,
        $original_booking['groom_name'],
        $original_booking['bride_name'],
        $original_booking['contact_number'],
        $original_booking['email']
    ]);
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Wedding rebooked successfully'
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 