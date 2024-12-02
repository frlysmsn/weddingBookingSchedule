<?php
require_once '../includes/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$booking_id = $_GET['booking_id'] ?? null;
if (!$booking_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing booking ID']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $query = "
        SELECT 
            b.*,
            COALESCE(u.name, 'N/A') as client_name,
            COALESCE(u.email, b.email) as client_email,
            (SELECT COUNT(*) FROM documents d WHERE d.booking_id = b.id) as total_docs,
            (SELECT COUNT(*) FROM documents d WHERE d.booking_id = b.id AND d.status = 'approved') as approved_docs
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $booking_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    
    if (!$booking) {
        throw new Exception('Booking not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $booking
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 