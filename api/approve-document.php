<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized']));
}

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
            u.email,
            u.name as user_name,
            dr.name as document_name
        FROM documents d
        JOIN users u ON d.user_id = u.id
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
        WHERE user_id = ?
    ");
    
    if (!$stmt->execute([$documentInfo['user_id']])) {
        throw new Exception('Failed to calculate progress');
    }
    
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    $progress = ($counts['total_count'] > 0) ? ($counts['approved_count'] / $counts['total_count']) * 100 : 0;

    // Send email notification
    $to = $documentInfo['email'];
    $subject = "Document " . ucfirst($status) . " - St. Rita Parish";
    
    $message = "Dear " . $documentInfo['user_name'] . ",\n\n";
    $message .= "Your document (" . $documentInfo['document_name'] . ") has been " . $status . ".\n";
    if ($remarks) {
        $message .= "Remarks: " . $remarks . "\n\n";
    }
    $message .= "You can check your document status in your dashboard.\n\n";
    $message .= "Best regards,\nSt. Rita Parish Team";

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