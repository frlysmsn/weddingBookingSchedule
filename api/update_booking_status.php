<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/send_email.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

$booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
$status = isset($_POST['status']) ? htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8') : null;

if(!$booking_id || !$status) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'error' => 'Invalid parameters']));
}

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE bookings SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    
    if (!$stmt->execute([$status, $booking_id])) {
        throw new Exception('Failed to update booking status');
    }

    $emailSent = sendEmailNotification($booking_id, $status);
    if (!$emailSent) {
        throw new Exception('Failed to send email notification');
    }

    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking status updated and email sent successfully'
    ]);

} catch(Exception $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    error_log('Booking Update Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update booking status'
    ]);
} 