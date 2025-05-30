<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db_connection.php';
 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['user_id'];
    
    $db = Database::getInstance()->getConnection();
    
    try {
        $stmt = $db->prepare("
            SELECT u.*, 
                   COUNT(b.id) as total_bookings,
                   MAX(b.created_at) as last_booking
            FROM users u
            LEFT JOIN bookings b ON u.id = b.user_id
            WHERE u.id = ?
            GROUP BY u.id
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Format the user details as HTML
            $html = "
                <div class='user-details'>
                    <div class='mb-3'>
                        <strong>Name:</strong> " . htmlspecialchars($user['name']) . "
                    </div>
                    <div class='mb-3 d-flex align-items-center'>
                        <i class='fas fa-envelope me-2'></i>
                        <strong>Email:</strong> <span class='ms-2'>" . htmlspecialchars($user['email']) . "</span>
                    </div>
                    <div class='mb-3 d-flex align-items-center'>
                        <i class='fas fa-user-tag me-2'></i>
                        <strong>Role:</strong> <span class='ms-2 badge bg-primary'>" . htmlspecialchars($user['role']) . "</span>
                    </div>
                    <div class='mb-3 d-flex align-items-center'>
                        <i class='fas fa-toggle-on me-2'></i>
                        <strong>Status:</strong> <span class='ms-2 badge " . ($user['active'] ? 'bg-success' : 'bg-danger') . "'>" . 
                        ($user['active'] ? 'Active' : 'Inactive') . "</span>
                    </div>
                    <div class='mb-3 d-flex align-items-center'>
                        <i class='fas fa-calendar-check me-2'></i>
                        <strong>Total Bookings:</strong> <span class='ms-2 badge bg-info'>" . $user['total_bookings'] . "</span>
                    </div>
                    <div class='mb-3 d-flex align-items-center'>
                        <i class='fas fa-clock me-2'></i>
                        <strong>Last Booking:</strong> <span class='ms-2'>" . 
                        ($user['last_booking'] ? date('M d, Y', strtotime($user['last_booking'])) : 'Never') . "</span>
                    </div>
                    <div class='mb-3 d-flex align-items-center'>
                        <i class='fas fa-calendar-plus me-2'></i>
                        <strong>Account Created:</strong> <span class='ms-2'>" . 
                        date('M d, Y', strtotime($user['created_at'])) . "</span>
                    </div>
                </div>";
            
            echo $html;
        } else {
            echo "<div class='alert alert-danger'>User not found</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error loading user details</div>";
    }
}
