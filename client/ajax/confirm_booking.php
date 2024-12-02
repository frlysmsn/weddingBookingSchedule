<?php
session_start();
require_once '../../includes/database.php';

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in");
    }

    $db = new Database();
    $conn = $db->getConnection();
    
    // Start transaction
    if (!mysqli_begin_transaction($conn)) {
        throw new Exception("Failed to start transaction");
    }
    
    // Combine name parts
    $bride_name = trim($_POST['bride_fname'] . ' ' . $_POST['bride_mname'] . ' ' . $_POST['bride_lname']);
    $groom_name = trim($_POST['groom_fname'] . ' ' . $_POST['groom_mname'] . ' ' . $_POST['groom_lname']);
    
    // Add user_id to the SQL query
    $sql = "INSERT INTO bookings (
        user_id, bride_name, bride_dob, bride_birthplace, bride_mother, bride_father,
        bride_prenup, bride_precana, groom_name, groom_dob, groom_birthplace,
        groom_mother, groom_father, groom_prenup, groom_precana,
        wedding_date, preferred_time, contact_number, email, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Add user_id to bind_param
    $stmt->bind_param("issssssssssssssssss",
        $_SESSION['user_id'],
        $bride_name,
        $_POST['bride_dob'],
        $_POST['bride_birthplace'],
        $_POST['bride_mother'],
        $_POST['bride_father'],
        $_POST['bride_prenup'],
        $_POST['bride_precana'],
        $groom_name,
        $_POST['groom_dob'],
        $_POST['groom_birthplace'],
        $_POST['groom_mother'],
        $_POST['groom_father'],
        $_POST['groom_prenup'],
        $_POST['groom_precana'],
        $_POST['wedding_date'],
        $_POST['preferred_time'],
        $_POST['contact_number'],
        $_POST['email']
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $booking_id = $stmt->insert_id;
    mysqli_commit($conn);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Booking submitted successfully!',
        'booking_id' => $booking_id
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        mysqli_rollback($conn);
    }
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to process booking: ' . $e->getMessage()
    ]);
}