<?php
session_start();
require 'C:/xampp/htdocs/softeng/PHPMailer-master/src/PHPMailer.php';
require 'C:/xampp/htdocs/softeng/PHPMailer-master/src/SMTP.php';
require 'C:/xampp/htdocs/softeng/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $otp = rand(100000, 999999); // Generate a 6-digit OTP
    $_SESSION['otp'] = $otp; // Store OTP in session

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Update with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'almerino.n.bscs@gmail.com'; // Update with your SMTP username
        $mail->Password = 'jcev kith vzte gcmr'; // Update with your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('almerino.n.bscs@gmail.com', 'EARIST Registration');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = 'Your OTP code is ' . $otp;

        $mail->send();
        echo 'OTP sent';
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
}
?>
