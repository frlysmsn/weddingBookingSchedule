<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $booking_id = $_POST['booking_id'] ?? null;
    
    if(!$booking_id) {
        throw new Exception('Missing booking ID');
    }
    
    // Get booking and user details
    $stmt = $db->prepare("
        SELECT 
            b.*,
            u.email,
            u.name as client_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$booking) {
        throw new Exception('Booking not found');
    }
    
    // Update booking status
    $stmt = $db->prepare("
        UPDATE bookings 
        SET status = 'approved', 
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$booking_id]);
    
    // Send confirmation email
    $to = $booking['email'];
    $subject = "Wedding Booking Confirmed - St. Rita Parish";
    
    $message = "Dear {$booking['client_name']},\n\n";
    $message .= "We are pleased to inform you that your wedding booking has been approved!\n\n";
    $message .= "Wedding Details:\n";
    $message .= "Date: " . date('F d, Y', strtotime($booking['wedding_date'])) . "\n";
    $message .= "Time: " . date('h:i A', strtotime($booking['preferred_time'])) . "\n\n";
    $message .= "Couple:\n";
    $message .= "Groom: {$booking['groom_name']}\n";
    $message .= "Bride: {$booking['bride_name']}\n\n";
    $message .= "Your journey towards the sanctity of marriage begins. We look forward to celebrating this blessed occasion with you.\n\n";
    $message .= "Best regards,\nSt. Rita Parish Wedding Ministry";
    
    mail($to, $subject, $message);
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking approved and confirmation email sent'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 