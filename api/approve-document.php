<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized']));
}

// Get and validate inputs
$document_id = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
$remarks = filter_input(INPUT_POST, 'remarks', FILTER_SANITIZE_STRING);

if(!$document_id || !$action) {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid parameters']));
}

$db = Database::getInstance()->getConnection();

try {
    $db->beginTransaction();

    // First, check if document exists and get related info
    $stmt = $db->prepare("
        SELECT 
            d.*,
            b.id as booking_id,
            u.email,
            u.name as user_name,
            dr.name as document_name
        FROM documents d
        JOIN bookings b ON d.booking_id = b.id
        JOIN users u ON b.user_id = u.id
        LEFT JOIN document_requirements dr ON d.document_type = dr.document_type
        WHERE d.id = ?
    ");
    
    if (!$stmt->execute([$document_id])) {
        throw new Exception('Failed to fetch document information');
    }
    
    $documentInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$documentInfo) {
        throw new Exception('Document not found');
    }

    // Update document status
    $status = ($action === 'approve') ? 'approved' : 'rejected';
    $stmt = $db->prepare("
        UPDATE documents 
        SET status = ?, 
            remarks = ?,
            updated_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    
    if (!$stmt->execute([$status, $remarks, $document_id])) {
        throw new Exception('Failed to update document status');
    }

    // Calculate new progress
    $stmt = $db->prepare("
        SELECT 
            COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
            COUNT(*) as total_count
        FROM documents 
        WHERE booking_id = ?
    ");
    
    if (!$stmt->execute([$documentInfo['booking_id']])) {
        throw new Exception('Failed to calculate progress');
    }
    
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($counts['total_count'] > 0) {
        $progress = ($counts['approved_count'] / $counts['total_count']) * 100;
    } else {
        $progress = 0;
    }

    // Update booking progress
    $stmt = $db->prepare("
        UPDATE bookings 
        SET document_progress = ? 
        WHERE id = ?
    ");
    
    if (!$stmt->execute([$progress, $documentInfo['booking_id']])) {
        throw new Exception('Failed to update booking progress');
    }

    // Prepare email notification
    $to = $documentInfo['email'];
    $subject = "Document " . ucfirst($status) . " - St. Rita Parish";
    
    if($action === 'approve') {
        $message = "Dear " . $documentInfo['user_name'] . ",\n\n";
        $message .= "Your document (" . $documentInfo['document_name'] . ") has been approved.\n";
        $message .= "You can check your document status in your dashboard.\n\n";
        $message .= "Best regards,\nSt. Rita Parish Team";
    } else {
        $message = "Dear " . $documentInfo['user_name'] . ",\n\n";
        $message .= "Your document (" . $documentInfo['document_name'] . ") has been rejected.\n";
        $message .= "Reason for rejection: " . $remarks . "\n\n";
        $message .= "Please update and resubmit your document.\n";
        $message .= "You can check your document status in your dashboard.\n\n";
        $message .= "Best regards,\nSt. Rita Parish Team";
    }

    // Try to send email but don't fail if it doesn't work
    @mail($to, $subject, $message);

    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Document has been ' . $status,
        'progress' => $progress,
        'status' => $status
    ]);

} catch(Exception $e) {
    $db->rollBack();
    error_log('Document Processing Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to process document',
        'details' => $e->getMessage()
    ]);
} 