<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $start = $_GET['start'] ?? date('Y-m-d');
    $end = $_GET['end'] ?? date('Y-m-d', strtotime('+1 year'));

    // Get approved bookings
    $stmt = $db->prepare("
        SELECT wedding_date, COUNT(*) as booking_count 
        FROM bookings 
        WHERE status = 'approved' 
        AND wedding_date BETWEEN ? AND ?
        GROUP BY wedding_date
    ");
    $stmt->execute([$start, $end]);
    
    $booked_dates = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $booked_dates[] = [
            'start' => $row['wedding_date'],
            'title' => 'Booked',
            'color' => '#6c757d',
            'display' => 'background'
        ];
    }

    echo json_encode($booked_dates);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Failed to fetch booked dates'
    ]);
}