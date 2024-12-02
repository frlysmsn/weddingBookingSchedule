<?php
session_start();
require_once '../../includes/database.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $allTimeSlots = ['08:00-09:00', '09:00-10:00', '13:00-14:00'];
    
    $query = "SELECT 
        DATE(wedding_date) as date,
        COUNT(*) as booking_count,
        GROUP_CONCAT(preferred_time) as booked_times
    FROM bookings 
    WHERE status IN ('pending', 'approved')
    GROUP BY DATE(wedding_date)";
             
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $date = $row['date'];
        $bookingCount = $row['booking_count'];
        $bookedTimes = explode(',', $row['booked_times']);
        
        if (count(array_unique($bookedTimes)) >= count($allTimeSlots)) {
            $events[] = [
                'id' => uniqid(),
                'title' => 'Fully Booked',
                'start' => $date,
                'display' => 'background',
                'backgroundColor' => '#ffcdd2'
            ];
        } else {
            $events[] = [
                'id' => uniqid(),
                'title' => 'Partially Booked',
                'start' => $date,
                'display' => 'background',
                'backgroundColor' => '#fff3cd'
            ];
        }
    }
    
    echo json_encode($events);
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'Failed to fetch booked dates']);
    exit;
}