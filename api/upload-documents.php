<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $booking_id = $_POST['booking_id'];
    $document_type = $_POST['document_type'];
    
    // Validate file type
    $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!in_array($file['type'], $allowed_types)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type']);
        exit;
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $upload_path = UPLOAD_PATH . 'documents/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO documents (booking_id, document_type, file_path) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$booking_id, $document_type, $filename])) {
            echo json_encode([
                'success' => true,
                'filename' => $filename
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Upload failed']);
    }
} 