<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db_connection.php';
require_once '../../includes/Mail.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    
    $booking_id = $_POST['booking_id'] ?? null;
    $status = $_POST['status'] ?? null;
    $reason = $_POST['reason'] ?? null;
    
    if (!$booking_id || !$status) {
        echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
        exit;
    }
    
    try {
        $db->beginTransaction();
        
        // Get booking details including user email
        $stmt = $db->prepare("
            SELECT b.*, b.email as user_email 
            FROM bookings b 
            WHERE b.id = ?
        ");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            throw new Exception('Booking not found');
        }

        // Update booking status
        $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$status, $booking_id]);

        // Record the action
        $stmt = $db->prepare("
            INSERT INTO booking_actions (booking_id, action_type, acted_by, reason) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$booking_id, $status, $_SESSION['user_id'], $reason]);

        // Send email if booking is approved
        if ($status === 'approved' && !empty($booking['user_email'])) {
            $mailer = new Mail();
            if ($mailer->sendBookingApproval($booking['user_email'], $booking)) {
                error_log("Email sent successfully to " . $booking['user_email']);
            } else {
                error_log("Failed to send email: " . $mailer->getError());
            }
        }
        
        $db->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Error in update_booking_status: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}