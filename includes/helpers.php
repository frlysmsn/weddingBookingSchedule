<?php

function formatDate($date, $format = 'F d, Y') {
    return date($format, strtotime($date));
}

function getStatusBadge($status) {
    $badges = [
        'pending' => 'status-badge status-pending',
        'approved' => 'status-badge status-approved',
        'rejected' => 'status-badge status-rejected'
    ];
    return $badges[$status] ?? 'status-badge';
}

function isDateAvailable($date) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM bookings 
        WHERE wedding_date = ? AND status != 'rejected'
        UNION ALL
        SELECT COUNT(*) FROM unavailable_dates 
        WHERE date = ?
    ");
    $stmt->execute([$date, $date]);
    return array_sum($stmt->fetchAll(PDO::FETCH_COLUMN)) === 0;
}

function validateDocument($file) {
    $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['valid' => false, 'error' => 'Invalid file type'];
    }
    
    if ($file['size'] > $max_size) {
        return ['valid' => false, 'error' => 'File too large'];
    }
    
    return ['valid' => true];
}

function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length));
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
} 