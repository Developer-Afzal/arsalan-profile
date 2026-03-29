<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get and sanitize form data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If validation fails, show errors
    if (!empty($errors)) {
        echo "<h3>Error:</h3>";
        foreach ($errors as $error) {
            echo "<p style='color:red'>❌ $error</p>";
        }
        echo "<br><a href='index.php'>Go back</a>";
        exit;
    }
    
    // Prepare email content
    $subject = "New Contact Form Submission from $name";
    
    // Create HTML email body
    $email_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
            .header { background: #007bff; color: white; padding: 15px; text-align: center; }
            .content { padding: 20px; background: white; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #007bff; }
            .value { margin-top: 5px; padding: 8px; background: #f5f5f5; border-radius: 4px; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Name:</div>
                    <div class='value'>$name</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'>$email</div>
                </div>
                <div class='field'>
                    <div class='label'>Phone:</div>
                    <div class='value'>$phone</div>
                </div>
                <div class='field'>
                    <div class='label'>Message:</div>
                    <div class='value'>" . nl2br($message) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Submitted:</div>
                    <div class='value'>" . date('F j, Y, g:i a') . "</div>
                </div>
            </div>
            <div class='footer'>
                This email was sent from your website contact form.
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Plain text version
    $text_body = "New Contact Form Submission\n\n";
    $text_body .= "Name: $name\n";
    $text_body .= "Email: $email\n";
    $text_body .= "Phone: $phone\n";
    $text_body .= "Message:\n$message\n";
    $text_body .= "\nSubmitted: " . date('F j, Y, g:i a');
    
    // Send email
    $result = smtp_mailer($_ENV['MY_MAIL'], $subject, $email_body, $text_body, $email, $name);
    
    // Display result to user
    if ($result === true) {
        echo "<h3 style='color:green'>✅ Message Sent Successfully!</h3>";
        echo "<p>Thank you $name for contacting us. We'll get back to you soon!</p>";
        echo "<br><a href='index.php'>Back to Homepage</a>";
    } else {
        echo "<h3 style='color:red'>❌ Error Sending Message</h3>";
        echo "<p>$result</p>";
        echo "<br><a href='index.php'>Try again</a>";
    }
}

function smtp_mailer($to, $subject, $html_msg, $text_msg, $reply_email, $reply_name) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 'tls'
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->Username = $_ENV['MY_MAIL'];  
        $mail->Password = "rgcvhjfmolyntxee";     
        
        // Sender and recipient
        $mail->setFrom($_ENV['MY_MAIL'], "Website Contact Form");
        $mail->addAddress($to);
        
        // Reply-to (so you can reply directly to the person who contacted you)
        $mail->addReplyTo($reply_email, $reply_name);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html_msg;
        $mail->AltBody = $text_msg;
        
        // SSL options (optional - for development only)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Send email
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>