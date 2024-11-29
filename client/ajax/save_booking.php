<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        $required_fields = ['groom_fname', 'groom_lname', 'bride_fname', 'bride_lname', 
                          'wedding_date', 'wedding_time', 'contact_number', 'email'];
        
        foreach($required_fields as $field) {
            if(empty($_POST[$field])) {
                throw new Exception("All required fields must be filled out");
            }
        }

        mysqli_begin_transaction($conn);

        // Check for existing pending booking
        $query = "SELECT id FROM bookings WHERE client_id = ? AND status = 'pending'";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existing_booking = mysqli_fetch_assoc($result);

        if($existing_booking) {
            // Update existing booking
            $query = "UPDATE bookings SET 
                     groom_fname = ?, groom_mname = ?, groom_lname = ?,
                     bride_fname = ?, bride_mname = ?, bride_lname = ?,
                     wedding_date = ?, wedding_time = ?,
                     contact_number = ?, email = ?, notes = ?,
                     updated_at = NOW()
                     WHERE id = ?";
            
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssssssssssi",
                $_POST['groom_fname'], $_POST['groom_mname'], $_POST['groom_lname'],
                $_POST['bride_fname'], $_POST['bride_mname'], $_POST['bride_lname'],
                $_POST['wedding_date'], $_POST['wedding_time'],
                $_POST['contact_number'], $_POST['email'], $_POST['notes'],
                $existing_booking['id']
            );
        } else {
            // Create new booking
            $query = "INSERT INTO bookings (
                     client_id, status, 
                     groom_fname, groom_mname, groom_lname,
                     bride_fname, bride_mname, bride_lname,
                     wedding_date, wedding_time,
                     contact_number, email, notes,
                     created_at
                     ) VALUES (?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "isssssssssss",
                $_SESSION['user_id'],
                $_POST['groom_fname'], $_POST['groom_mname'], $_POST['groom_lname'],
                $_POST['bride_fname'], $_POST['bride_mname'], $_POST['bride_lname'],
                $_POST['wedding_date'], $_POST['wedding_time'],
                $_POST['contact_number'], $_POST['email'], $_POST['notes']
            );
        }

        mysqli_stmt_execute($stmt);
        mysqli_commit($conn);
        
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} 