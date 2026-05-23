<?php
header('Content-Type: application/json');
session_start();
require 'db_conn.php';

require 'includes/Exception.php';
require 'includes/PHPMailer.php';
require 'includes/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST['identifier'] ?? '';

    if(empty($identifier)){
        echo json_encode(["status" => "error", "message" => "Please enter username."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $username = $row['username'];
        $otp = (string)rand(100000, 999999);

        $_SESSION['forgot_otp'] = $otp;
        $_SESSION['forgot_otp_expiry'] = time() + 300;
        $_SESSION['forgot_user'] = $username;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'junhengtoh@gmail.com'; 
            $mail->Password   = 'iagt ctrs ggfa dkvg'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('playlearn@mmu.edu.my', 'PlayLearn Support');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Verify your PlayLearn Account Recovery';

            // ✅ 修改这里：使用与图片 1 一致的 HTML 布局
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: auto;'>
                    <h2 style='color: #4A90E2;'>Account Recovery OTP</h2>
                    <p style='color: #333;'>Hello! Your verification code for PlayLearn recovery is:</p>
                    <div style='background: #f9f9f9; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px; border-radius: 5px; color: #333;'>
                        $otp
                    </div>
                    <p style='color: #888; font-size: 12px; margin-top: 20px;'>
                        This code will expire in 5 minutes. Please do not share it with anyone.
                    </p>
                </div>
            ";
            
            $mail->send();

            $atPos = strpos($email, "@");
            $namePart = substr($email, 0, $atPos);
            $domainPart = substr($email, $atPos);
            $maskedEmail = substr($namePart, 0, 1) . "****" . substr($namePart, -1) . $domainPart;

            echo json_encode([
                "status" => "success", 
                "message" => "OTP sent",
                "username" => $username,
                "masked_email" => $maskedEmail
            ]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Mail error: " . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Username not found."]);
    }
    $stmt->close();
}
$conn->close();
?>