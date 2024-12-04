<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Mail {
    private $mailer;
    private $error;

    public function __construct() {
        try {
            $this->mailer = new PHPMailer(true);
            
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
            error_log("Mail Constructor Error: " . $e->getMessage());
            throw $e;
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

    public function sendBookingRejection($userEmail, $bookingDetails, $reason) {
        try {
            if (!$userEmail || !$bookingDetails || !$reason) {
                throw new Exception('Missing required email, booking details, or reason');
            }

            $this->mailer->clearAddresses();
            $this->mailer->addAddress($userEmail);

            $this->mailer->Subject = 'Your Wedding Booking Has Been Rejected';
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.8; max-width: 600px; margin: 0 auto; background-color: #f4f4f9; padding: 20px;'>
                    <div style='background-color: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 30px;'>
                        <h2 style='color: #FF0000; text-align: center; font-size: 24px;'><i class='fas fa-exclamation-circle'></i> Important Notice Regarding Your Wedding Booking</h2>
                        <p style='font-size: 16px;'>Dear <strong>{$bookingDetails['groom_name']}</strong> and <strong>{$bookingDetails['bride_name']}</strong>,</p>
                        <p style='font-size: 16px;'>We regret to inform you that your wedding booking has been rejected. Below is the reason provided by our admin:</p>
                        <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                            <p style='margin: 0; font-size: 16px;'><strong>Reason for Rejection:</strong> {$reason}</p>
                        </div>
                        <p style='font-size: 16px;'>Please feel free to contact us for further assistance or clarification.</p>
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

    public function sendVerificationCode($email, $code) {
        try {
            if (!$this->mailer) {
                echo "Error: Mailer not initialized properly<br>";
                throw new Exception('Mailer not initialized properly');
            }

            echo "Attempting to send email to: " . $email . "<br>";
            
            // Clear all recipients first
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Email Verification - St. Rita Parish';
            
            // Email template
            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2>Email Verification</h2>
                    <p>Thank you for registering with St. Rita Parish Wedding Booking System.</p>
                    <p>Your verification code is:</p>
                    <div style='background: #f4f4f4; padding: 15px; text-align: center; font-size: 24px; letter-spacing: 5px; margin: 20px 0;'>
                        <strong>{$code}</strong>
                    </div>
                    <p>Please enter this code to verify your email address.</p>
                    <p>If you didn't request this verification, please ignore this email.</p>
                </div>
            ";
            
            $this->mailer->Body = $body;
            $this->mailer->AltBody = "Your verification code is: {$code}";
            
            $result = $this->mailer->send();
            if (!$result) {
                echo "Mailer Error: " . $this->mailer->ErrorInfo . "<br>";
                throw new Exception($this->mailer->ErrorInfo);
            }
            echo "Email sent successfully!<br>";
            return true;
            
        } catch (Exception $e) {
            echo "Exception caught: " . $e->getMessage() . "<br>";
            $this->error = $e->getMessage();
            error_log("Mail sending error: " . $this->error);
            return false;
        }
    }

    public function getError() {
        return $this->error;
    }

    public function sendPasswordReset($email, $reset_link) {
        try {
            // Set email content
            $this->mailer->setFrom(SMTP_USERNAME, SITE_NAME);
            $this->mailer->addAddress($email);
            $this->mailer->isHTML(true);
            
            $this->mailer->Subject = "Password Reset Request - " . SITE_NAME;
            
            // HTML email body
            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2>Password Reset Request</h2>
                    <p>Hello,</p>
                    <p>We received a request to reset your password. Click the button below to reset your password:</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='{$reset_link}' 
                           style='background-color: #007bff; 
                                  color: white; 
                                  padding: 12px 25px; 
                                  text-decoration: none; 
                                  border-radius: 5px;
                                  display: inline-block;'>
                            Reset Password
                        </a>
                    </p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you did not request this reset, please ignore this email.</p>
                    <p>Best regards,<br>" . SITE_NAME . "</p>
                </div>";
            
            $this->mailer->Body = $body;
            $this->mailer->AltBody = "Reset your password by clicking this link: {$reset_link}";
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
} 
