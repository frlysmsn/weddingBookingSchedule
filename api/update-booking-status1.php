<?php
session_start();
require_once '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $bookingId = $_POST['booking_id'];
    $status = $_POST['status'];

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $bookingId])) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false]);
    }
} else {
    http_response_code(403);
    echo json_encode(['success' => false]);
} 