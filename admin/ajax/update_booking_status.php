<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $reason = isset($_POST['reason']) ? mysqli_real_escape_string($conn, $_POST['reason']) : null;
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Update booking status
        $query = "UPDATE bookings SET 
                 status = ?, 
                 rejection_reason = ?,
                 updated_at = NOW(),
                 updated_by = ?
                 WHERE id = ?";
                 
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssii", $status, $reason, $_SESSION['user_id'], $booking_id);
        mysqli_stmt_execute($stmt);
        
        // Get client email for notification
        $query = "SELECT u.email, u.first_name, b.wedding_date 
                 FROM bookings b 
                 JOIN users u ON b.client_id = u.id 
                 WHERE b.id = ?";
                 
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        
        // Send email notification
        $subject = $status === 'approved' ? 
                  "Wedding Booking Approved" : 
                  "Wedding Booking Status Update";
                  
        $message = $status === 'approved' ? 
                  "Dear {$user['first_name']},\n\nYour wedding booking for " . date('F d, Y', strtotime($user['wedding_date'])) . " has been approved." : 
                  "Dear {$user['first_name']},\n\nYour wedding booking for " . date('F d, Y', strtotime($user['wedding_date'])) . " has been rejected.\n\nReason: {$reason}";
                  
        mail($user['email'], $subject, $message);
        
        mysqli_commit($conn);
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} 