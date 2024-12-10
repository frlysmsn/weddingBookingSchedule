<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

// Check if user is logged in and is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if document ID is provided
$document_id = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT);
if(!$document_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid document ID']);
    exit();
}

try {
    $db = Database::getInstance()->getConnection();
    
    // First, get the document file path
    $stmt = $db->prepare("SELECT file_path FROM documents WHERE id = ?");
    $stmt->execute([$document_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$document) {
        throw new Exception('Document not found');
    }
    
    // Delete the physical file if it exists
    if ($document['file_path'] && file_exists('../uploads/' . $document['file_path'])) {
        unlink('../uploads/' . $document['file_path']);
    }
    
    // Delete the database record
    $stmt = $db->prepare("DELETE FROM documents WHERE id = ?");
    $stmt->execute([$document_id]);
    
    echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error deleting document: ' . $e->getMessage()]);
} 