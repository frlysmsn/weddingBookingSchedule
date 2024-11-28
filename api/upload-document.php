<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '../error.log');

require_once '../includes/config.php';
require_once '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Debug information
    error_log("Upload attempt by user: " . $_SESSION['user_id']);
    error_log("FILES array: " . print_r($_FILES, true));
    error_log("POST array: " . print_r($_POST, true));

    // Validate file upload
    if (!isset($_FILES['document'])) {
        throw new Exception('No file received');
    }

    if ($_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = array(
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        );
        throw new Exception('Upload error: ' . 
            ($upload_errors[$_FILES['document']['error']] ?? 'Unknown error'));
    }

    // Validate document type
    $document_type = $_POST['document_type'] ?? '';
    if (empty($document_type)) {
        throw new Exception('Document type is required');
    }

    // Get or create booking
    $stmt = $db->prepare("
        SELECT id FROM bookings 
        WHERE user_id = ? 
        AND status != 'cancelled' 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        // Create a temporary booking
        $stmt = $db->prepare("
            INSERT INTO bookings (user_id, status, created_at) 
            VALUES (?, 'pending', NOW())
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $booking_id = $db->lastInsertId();
        error_log("Created new booking: " . $booking_id);
    } else {
        $booking_id = $booking['id'];
        error_log("Using existing booking: " . $booking_id);
    }

    // Ensure upload directory exists
    $upload_dir = __DIR__ . '/../uploads/documents/' . $booking_id;
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception('Failed to create upload directory');
        }
        error_log("Created directory: " . $upload_dir);
    }

    // Generate unique filename
    $filename = $document_type . '_' . time() . '.pdf';
    $file_path = $upload_dir . '/' . $filename;
    error_log("Target file path: " . $file_path);

    // Start transaction
    $db->beginTransaction();

    try {
        // Check for existing document
        $stmt = $db->prepare("
            SELECT id FROM documents 
            WHERE booking_id = ? AND document_type = ?
        ");
        $stmt->execute([$booking_id, $document_type]);
        $existing_doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_doc) {
            // Update existing document
            $stmt = $db->prepare("
                UPDATE documents 
                SET file_path = ?, 
                    status = 'pending',
                    created_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$file_path, $existing_doc['id']]);
            error_log("Updated existing document: " . $existing_doc['id']);
        } else {
            // Insert new document
            $stmt = $db->prepare("
                INSERT INTO documents (
                    booking_id, 
                    document_type, 
                    file_path,
                    status, 
                    created_at
                ) VALUES (?, ?, ?, 'pending', NOW())
            ");
            $stmt->execute([$booking_id, $document_type, $file_path]);
            error_log("Inserted new document: " . $db->lastInsertId());
        }

        // Move uploaded file
        if (!move_uploaded_file($_FILES['document']['tmp_name'], $file_path)) {
            throw new Exception('Failed to move uploaded file');
        }

        $db->commit();
        error_log("Transaction committed successfully");

        echo json_encode([
            'success' => true,
            'message' => 'Document uploaded successfully'
        ]);

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Error in upload process: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 