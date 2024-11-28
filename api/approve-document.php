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
    
    $document_id = $_POST['document_id'] ?? null;
    $action = $_POST['action'] ?? null;
    $remarks = $_POST['remarks'] ?? null;
    
    if(!$document_id || !$action) {
        throw new Exception('Missing required parameters');
    }
    
    // Get document and user details
    $stmt = $db->prepare("
        SELECT 
            d.*,
            u.email as client_email,
            u.name as client_name
        FROM documents d
        JOIN bookings b ON d.booking_id = b.id
        JOIN users u ON b.user_id = u.id
        WHERE d.id = ?
    ");
    $stmt->execute([$document_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$document) {
        throw new Exception('Document not found');
    }
    
    // Update document status
    $stmt = $db->prepare("
        UPDATE documents 
        SET status = ?, remarks = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([
        $action === 'approve' ? 'approved' : 'rejected',
        $remarks,
        $document_id
    ]);
    
    // Send email notification
    $to = $document['client_email'];
    $subject = "Document " . ($action === 'approve' ? 'Approved' : 'Rejected');
    
    $message = "Dear {$document['client_name']},\n\n";
    $message .= $action === 'approve' 
        ? "Your document has been approved. You can now proceed with your wedding booking."
        : "Your document has been rejected for the following reason:\n{$remarks}\n\nPlease submit an updated document.";
    
    mail($to, $subject, $message);
    
    echo json_encode([
        'success' => true,
        'message' => 'Document ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 