<?php
require_once 'includes/Mail.php';

$mailer = new Mail();
$success = $mailer->sendBookingApproval('test@example.com', [
    'wedding_date' => '2023-12-25',
    'preferred_time' => '10:00 AM' 
]);

if ($success) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
} 
