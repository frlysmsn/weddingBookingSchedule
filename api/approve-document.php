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
$action = isset($_POST['action']) ? htmlspecialchars($_POST['action'], ENT_QUOTES, 'UTF-8') : null;
$remarks = isset($_POST['remarks']) ? htmlspecialchars($_POST['remarks'], ENT_QUOTES, 'UTF-8') : '';

if(!$document_id || !$action) {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid parameters']));
}

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    // Get document and user information
    $stmt = $db->prepare("
        SELECT d.*, u.email, u.name as user_name, u.id as user_id
        FROM documents d
        JOIN users u ON d.user_id = u.id
        WHERE d.id = ?
    ");
    
    if (!$stmt->execute([$document_id])) {
        throw new Exception('Failed to fetch document information');
    }
    
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$doc) {
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
    
    if (!$stmt->execute([$doc['user_id']])) {
        throw new Exception('Failed to calculate progress');
    }
    
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    $progress = ($counts['total_count'] > 0) ? ($counts['approved_count'] / $counts['total_count']) * 100 : 0;

    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Document has been ' . $status,
        'progress' => $progress,
        'approved_count' => $counts['approved_count'],
        'total_count' => $counts['total_count'],
        'user_id' => $doc['user_id']
    ]);

} catch(Exception $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    error_log('Document Processing Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to process document',
        'details' => $e->getMessage()
    ]);
}