<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Adjust the path as per your directory structure
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bhai.roky0987@gmail.com'; // your Gmail
        $mail->Password = 'efoc fbdd mgwb fujs'; // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('bhai.roky0987@gmail.com', 'Prime Report OTP');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Prime Report Registration';
        $mail->Body = "<h3>Your OTP is: $otp</h3>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
