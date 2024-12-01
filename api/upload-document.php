<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

header('Content-Type: application/json');

function logDebug($message) {
    $logFile = __DIR__ . '/upload_errors.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated');
    }

    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    if (!isset($_POST['document_type'])) {
        throw new Exception('Document type not specified');
    }

    $document_type = $_POST['document_type'];
    $db = Database::getInstance()->getConnection();
    
    // Start transaction
    $db->beginTransaction();

    try {
        // Create upload directory if it doesn't exist
        $upload_dir = __DIR__ . '/../uploads/documents/' . $_SESSION['user_id'];
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                throw new Exception("Failed to create directory: " . $upload_dir);
            }
        }

        // Generate filename and move uploaded file
        $filename = $document_type . '_' . time() . '.pdf';
        $file_path = $upload_dir . '/' . $filename;
        
        if (!move_uploaded_file($_FILES['document']['tmp_name'], $file_path)) {
            throw new Exception('Failed to move uploaded file');
        }

        // Delete existing document of same type if exists
        $stmt = $db->prepare("
            DELETE FROM documents 
            WHERE user_id = ? AND document_type = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $document_type]);

        // Insert new document
        $stmt = $db->prepare("
            INSERT INTO documents (
                user_id,
                document_type,
                file_path,
                status,
                created_at
            ) VALUES (?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $document_type,
            'uploads/documents/' . $_SESSION['user_id'] . '/' . $filename
        ]);

        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Document uploaded successfully'
        ]);

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 