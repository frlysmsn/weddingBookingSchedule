<?php
require_once '../../includes/database.php';

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT wedding_date FROM bookings WHERE status != 'cancelled'";
$result = $conn->query($sql);

$booked_dates = array();
while($row = $result->fetch_assoc()) {
    $booked_dates[] = $row['wedding_date'];
}

echo json_encode($booked_dates); 