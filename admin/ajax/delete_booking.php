<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db_connection.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    
    $booking_id = $_POST['booking_id'] ?? null;
    
    if (!$booking_id) {
        echo json_encode(['success' => false, 'error' => 'Missing booking ID']);
        exit;
    }
    
    try {
        $db->beginTransaction();
        
        // First, delete related records in booking_actions
        $stmt = $db->prepare("DELETE FROM booking_actions WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        
        // Then delete the booking
        $stmt = $db->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        
        $db->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}