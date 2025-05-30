<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db_connection.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403); 
    exit('Unauthorized');
}

if (isset($_POST['booking_id'])) {
    $db = Database::getInstance()->getConnection();
    
    // Fetch booking details with user information
    $stmt = $db->prepare("
        SELECT b.*, u.name, u.email,
            (SELECT COUNT(*) FROM documents d 
             WHERE d.booking_id = b.id 
             AND d.status = 'approved') as approved_docs
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    
    $stmt->execute([$_POST['booking_id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($booking) {
        // Format the response HTML
        include '../views/partials/booking_details_modal.php';
    } else {
        echo "Booking not found";
    }
}
