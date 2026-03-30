<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables (optional)
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

function env($key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

try {
    $mail = new PHPMailer(true);
    
    // Configure for Render's internal SMTP (free tier)
    $mail->isSMTP();
    $mail->Host = 'smtp.render.com';
    $mail->Port = 25;  // Internal port
    $mail->SMTPAuth = false;
    $mail->SMTPSecure = false;
    $mail->SMTPAutoTLS = false;
    
    // Increase timeout for reliability
    $mail->Timeout = 30;
    
    // FROM address - must be verified in Render dashboard
    // You can verify your Gmail address in Render dashboard
    $mail->setFrom('afzalansari891gmail@gmail.com', 'Afzal Ansari');
    
    // TO address
    $mail->addAddress('afzalansari891gmail@gmail.com', 'Afzal Ansari');
    
    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from Render Free Tier';
    $mail->Body = '<h1>Hello!</h1><p>This email was sent using Render\'s internal SMTP.</p>';
    $mail->AltBody = 'This email was sent using Render\'s internal SMTP.';
    
    $mail->send();
    echo '✅ Email sent successfully via Render internal SMTP!';
    
} catch (Exception $e) {
    echo "❌ Mailer Error: {$mail->ErrorInfo}";
    error_log("Mailer Error: {$mail->ErrorInfo}");
}