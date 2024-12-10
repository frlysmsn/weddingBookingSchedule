<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/Mail.php';

$response = ['success' => false, 'message' => ''];

try {
    // Check if session exists
    if (!isset($_SESSION['temp_user_id'])) {
        throw new Exception('Invalid session. Please try registering again.');
    }

    $user_id = $_SESSION['temp_user_id'];
    $db = Database::getInstance()->getConnection();

    // Start transaction
    $db->beginTransaction();

    // Fetch user email
    $stmt = $db->prepare("SELECT email, verification_code FROM users WHERE id = ?");
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

    // Try to send the verification email
    $mailer = new Mail();
    $emailSent = $mailer->sendVerificationCode($email, $verification_code);

    if (!$emailSent) {
        // If email fails, rollback the database changes and throw exception
        $db->rollBack();
        throw new Exception('Failed to send verification code: ' . $mailer->getError());
    }

    // If we got here, both database update and email sending were successful
    $db->commit();
    $response['success'] = true;
    $response['message'] = 'Verification code sent successfully to your email.';

} catch (Exception $e) {
    // Ensure transaction is rolled back on error
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Resend verification code error: " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
exit; 