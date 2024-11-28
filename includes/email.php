<?php
require 'vendor/autoload.php';
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
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
    }

    public function sendBookingConfirmation($to, $name, $date) {
        try {
            $this->mailer->setFrom(SMTP_USER, SITE_NAME);
            $this->mailer->addAddress($to);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Wedding Booking Confirmation';
            $this->mailer->Body = $this->getBookingTemplate($name, $date);
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    private function getBookingTemplate($name, $date) {
        return "
            <h2>Wedding Booking Confirmation</h2>
            <p>Dear {$name},</p>
            <p>Your wedding booking for {$date} has been confirmed.</p>
            <p>Please keep this email for your records.</p>
            <br>
            <p>Best regards,</p>
            <p>" . SITE_NAME . "</p>
        ";
    }
}
