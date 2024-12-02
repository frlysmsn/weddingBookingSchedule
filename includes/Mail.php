<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Mail {
    private $mailer;
    private $error;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'stritaparishwedding@gmail.com';
            $this->mailer->Password = 'oler wsfv qxvt hvkf';
            $this->mailer->SMTPSecure = 'tls';
            $this->mailer->Port = 587;
            $this->mailer->SMTPDebug = 0;
            
            // Default settings
            $this->mailer->isHTML(true);
            $this->mailer->setFrom('stritaparishwedding@gmail.com', 'St. Rita Parish Wedding Station');
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log("Mail initialization error: " . $this->error);
        }
    }

    public function sendBookingApproval($userEmail, $bookingDetails) {
        try {
            if (!$userEmail || !$bookingDetails) {
                throw new Exception('Missing required email or booking details');
            }

            $this->mailer->clearAddresses();
            $this->mailer->addAddress($userEmail);
            
            $this->mailer->Subject = 'Your Wedding Booking is Approved!';
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.8; max-width: 600px; margin: 0 auto; background-color: #f4f4f9; padding: 20px;'>
                    <div style='background-color: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 30px;'>
                        <h2 style='color: #4CAF50; text-align: center; font-size: 24px;'><i class='fas fa-heart'></i> Congratulations on Your Upcoming Wedding!</h2>
                        <p style='font-size: 16px;'>Dear <strong>{$bookingDetails['groom_name']}</strong> and <strong>{$bookingDetails['bride_name']}</strong>,</p>
                        <p style='font-size: 16px;'>We are thrilled to inform you that your wedding booking has been approved! We are excited to be a part of your special day and can't wait to celebrate this joyous occasion with you.</p>
                        <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                            <p style='margin: 0; font-size: 16px;'><i class='fas fa-calendar-alt'></i> <strong>Wedding Date:</strong> {$bookingDetails['wedding_date']}</p>
                            <p style='margin: 0; font-size: 16px;'><i class='fas fa-clock'></i> <strong>Time:</strong> {$bookingDetails['preferred_time']}</p>
                        </div>
                        <p style='font-size: 16px;'>As you prepare for your big day, please remember to visit us at the church office to submit the physical copies of the documents you have uploaded.</p>
                        <p style='font-size: 16px;'>We look forward to seeing you soon and wish you all the best as you embark on this beautiful journey together!</p>
                        <p style='margin-top: 20px; font-size: 16px;'>Warm regards,</p>
                        <p style='font-weight: bold; font-size: 16px;'>St. Rita Parish Wedding Station</p>
                    </div>
                </div>
            ";
            
            $success = $this->mailer->send();
            if (!$success) {
                throw new Exception($this->mailer->ErrorInfo);
            }
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log("Mail Error: {$this->error}");
            return false;
        }
    }
} 
