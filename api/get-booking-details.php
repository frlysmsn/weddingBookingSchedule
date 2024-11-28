<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

if (isset($_GET['booking_id'])) {
    $db = Database::getInstance()->getConnection();
    
    // Fetch booking details with user information
    $stmt = $db->prepare("
        SELECT b.*, u.name, u.email,
            (SELECT GROUP_CONCAT(CONCAT(document_type, ':', file_path))
             FROM documents 
             WHERE booking_id = b.id) as documents
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    
    $stmt->execute([$_GET['booking_id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($booking) {
        // Format the details for display
        $html = "
            <div class='booking-details'>
                <p><strong>Client Name:</strong> {$booking['name']}</p>
                <p><strong>Email:</strong> {$booking['email']}</p>
                <p><strong>Wedding Date:</strong> " . date('F d, Y', strtotime($booking['wedding_date'])) . "</p>
                <p><strong>Status:</strong> " . ucfirst($booking['status']) . "</p>
                <p><strong>Booking Date:</strong> " . date('F d, Y', strtotime($booking['created_at'])) . "</p>
                
                <h4>Uploaded Documents:</h4>
                <ul>";
        
        if ($booking['documents']) {
            $documents = explode(',', $booking['documents']);
            foreach ($documents as $doc) {
                list($type, $path) = explode(':', $doc);
                $html .= "<li>
                    {$type}: <a href='uploads/documents/{$path}' target='_blank'>View Document</a>
                </li>";
            }
        } else {
            $html .= "<li>No documents uploaded</li>";
        }
        
        $html .= "</ul></div>";
        
        echo $html;
    } else {
        echo "Booking not found";
    }
}