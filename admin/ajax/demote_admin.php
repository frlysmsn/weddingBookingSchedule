<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db_connection.php';
 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $db = Database::getInstance()->getConnection();

    try {
        $stmt = $db->prepare("UPDATE users SET role = 'client' WHERE id = ? AND email != 'admin@admin.com'");
        $stmt->execute([$userId]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
