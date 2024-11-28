<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $date = $_GET['date'] ?? date('Y-m-d');

    // Get booked time slots for the selected date
    $stmt = $db->prepare("
        SELECT preferred_time 
        FROM bookings 
        WHERE wedding_date = ? 
        AND status != 'cancelled'
    ");
    $stmt->execute([$date]);
    $booked_slots = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'success' => true,
        'booked_slots' => $booked_slots
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Failed to fetch available time slots'
    ]);
} 