<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/Mail.php';

$response = ['success' => false, 'message' => ''];

try {
    if (!isset($_SESSION['temp_user_id'])) {
        throw new Exception('Invalid session. Please try registering again.');
    }

    $user_id = $_SESSION['temp_user_id'];
    $db = Database::getInstance()->getConnection();

    // Fetch user email
    $stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('User not found.');
    }

    $email = $user['email'];
    $verification_code = sprintf("%06d", mt_rand(1, 999999));

    // Update verification code in the database
    $stmt = $db->prepare("UPDATE users SET verification_code = ? WHERE id = ?");
    if (!$stmt->execute([$verification_code, $user_id])) {
        throw new Exception('Failed to update verification code.');
    }

    // Send verification email
    $mailer = new Mail();
    if (!$mailer->sendVerificationCode($email, $verification_code)) {
        throw new Exception('Failed to send verification code: ' . $mailer->getError());
    }

    $response['success'] = true;
    $response['message'] = 'Verification code resent successfully.';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Resend verification code error: " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
exit; 