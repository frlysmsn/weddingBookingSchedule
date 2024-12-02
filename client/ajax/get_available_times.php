<?php
session_start();
require_once '../../includes/database.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['date'])) {
        throw new Exception('Date parameter is required');
    }
    
    $date = $_GET['date'];
    $db = new Database();
    $conn = $db->getConnection();
    
    // All available time slots
    $allTimeSlots = ['08:00-09:00', '09:00-10:00', '13:00-14:00'];
    
    // Get booked times for the date
    $query = "SELECT preferred_time 
              FROM bookings 
              WHERE DATE(wedding_date) = ? 
              AND status IN ('pending', 'approved')";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookedTimes = [];
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
        $bookedTimes[] = $row[0];
    }
    
    // Get available times
    $availableTimes = array_values(array_diff($allTimeSlots, $bookedTimes));
    
    // Return both available and booked times
    echo json_encode([
        'success' => true,
        'available_times' => $availableTimes,
        'booked_times' => $bookedTimes,
        'date' => $date
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_available_times.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 