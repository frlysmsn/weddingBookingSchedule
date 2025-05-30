<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class EmailNotification { 
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = SMTP_USER;
        $this->mailer->Password = SMTP_PASS;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = SMTP_PORT;
        
        // Default settings
        $this->mailer->isHTML(true);
        $this->mailer->setFrom(SMTP_FROM_EMAIL, SITE_NAME);
    }

    public function sendBookingConfirmation($booking_id) {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT b.*, u.email, u.name 
            FROM bookings b 
            JOIN users u ON b.user_id = u.id 
            WHERE b.id = ?
        ");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->mailer->addAddress($booking['email']);
        $this->mailer->Subject = 'Wedding Booking Confirmation - ' . SITE_NAME;
        
        $body = $this->getBookingConfirmationTemplate($booking);
        $this->mailer->Body = $body;
        
        return $this->mailer->send();
    }

    public function sendStatusUpdate($booking_id, $status) {
        // Similar to above but with status update template
    }

    public function sendAdminNotification($booking_id) {
        // Notify admin about new booking
    }

    private function getBookingConfirmationTemplate($booking) {
        return "
            <h2>Booking Confirmation</h2>
            <p>Dear {$booking['name']},</p>
            <p>Your wedding booking has been received successfully.</p>
            
            <h3>Booking Details:</h3>
            <ul>
                <li>Wedding Date: " . date('F d, Y', strtotime($booking['wedding_date'])) . "</li>
                <li>Time: " . date('h:i A', strtotime($booking['preferred_time'])) . "</li>
                <li>Couple: {$booking['groom_name']} & {$booking['bride_name']}</li>
            </ul>

            <p>Please complete the required documents checklist in your dashboard.</p>
            
            <p>Thank you for choosing " . SITE_NAME . ".</p>
        ";
    }
} 
