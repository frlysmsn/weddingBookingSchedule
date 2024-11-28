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
    
    // Validate required fields
    $required_fields = [
        'wedding_date',
        'preferred_time',
        'groom_name',
        'bride_name',
        'contact_number',
        'email'
    ];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Check if date and time slot is still available
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM bookings 
        WHERE wedding_date = ? 
        AND preferred_time = ? 
        AND status != 'cancelled'
    ");
    $stmt->execute([$_POST['wedding_date'], $_POST['preferred_time']]);
    
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Selected date and time is no longer available');
    }

    // Check required documents
    $stmt = $db->prepare("
        SELECT document_type 
        FROM documents 
        WHERE user_id = ? AND status = 'approved'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $approved_docs = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $missing_docs = array_diff(
        ['baptismal', 'confirmation', 'marriage_license', 'birth_certificate', 'cenomar'],
        $approved_docs
    );

    if (!empty($missing_docs)) {
        throw new Exception('Please upload all required documents before booking');
    }

    // Insert booking
    $stmt = $db->prepare("
        INSERT INTO bookings (
            user_id,
            wedding_date,
            preferred_time,
            groom_name,
            bride_name,
            contact_number,
            email,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");

    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['wedding_date'],
        $_POST['preferred_time'],
        $_POST['groom_name'],
        $_POST['bride_name'],
        $_POST['contact_number'],
        $_POST['email']
    ]);

    $booking_id = $db->lastInsertId();

    // Send notification to admin (we should implement this)
    // sendAdminNotification($booking_id);

    echo json_encode([
        'success' => true,
        'message' => 'Booking submitted successfully',
        'booking_id' => $booking_id
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

function sendAdminNotification($booking_id) {
    // TODO: Implement email notification to admin
    // This should be implemented to alert admins of new bookings
} 