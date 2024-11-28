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

    logDebug("Upload attempt started");
    logDebug("User ID: " . $_SESSION['user_id']);
    logDebug("POST data: " . print_r($_POST, true));
    logDebug("FILES data: " . print_r($_FILES, true));

    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    if (!isset($_POST['document_type'])) {
        throw new Exception('Document type not specified');
    }

    $document_type = $_POST['document_type'];
    logDebug("Document type: " . $document_type);

    $db = Database::getInstance()->getConnection();

    // First, verify the user exists
    $stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid user ID');
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

    $booking_id = null;

    // Start transaction
    $db->beginTransaction();
    logDebug("Transaction started");

    try {
        if (!$booking) {
            logDebug("Creating new booking");
            // Create new booking with required user_id and wedding_date
            $stmt = $db->prepare("
                INSERT INTO bookings (
                    user_id, 
                    wedding_date,
                    status, 
                    created_at
                ) VALUES (
                    ?, 
                    DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY),
                    'pending', 
                    NOW()
                )
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $booking_id = $db->lastInsertId();
            logDebug("Created new booking: " . $booking_id);
        } else {
            $booking_id = $booking['id'];
            logDebug("Using existing booking ID: " . $booking_id);
        }

        // Create upload directory if it doesn't exist
        $upload_dir = __DIR__ . '/../uploads/documents/' . $booking_id;
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                throw new Exception("Failed to create directory: " . $upload_dir);
            }
            logDebug("Created directory: " . $upload_dir);
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
            WHERE booking_id = ? AND document_type = ?
        ");
        $stmt->execute([$booking_id, $document_type]);

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
        $stmt->execute([
            $booking_id,
            $document_type,
            'uploads/documents/' . $booking_id . '/' . $filename
        ]);

        $db->commit();
        logDebug("Transaction committed successfully");

        echo json_encode([
            'success' => true,
            'message' => 'Document uploaded successfully'
        ]);

    } catch (Exception $e) {
        $db->rollBack();
        logDebug("Transaction rolled back: " . $e->getMessage());
        throw $e;
    }

} catch (Exception $e) {
    logDebug("Error occurred: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 