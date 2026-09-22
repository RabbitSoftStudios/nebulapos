<?php
/**
 * TEST EMAIL SENDER
 */

require_once __DIR__ . '/../../../../../vendor/autoload.php';
$config = require __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "Testing SMTP Config...\n";
echo "Email: " . $config['SMTP_EMAIL'] . "\n";
// Don't echo password
echo "Host: smtp.gmail.com\n";
echo "Port: 587\n";

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 3;                      // Enable verbose debug output (3 = Client + Server)
    $mail->Debugoutput = 'echo';               // Output to console
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = $config['SMTP_EMAIL'];                  // SMTP username
    $mail->Password   = $config['SMTP_PASSWORD'];               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    $mail->CharSet    = 'UTF-8';

    // Recipients
    $mail->setFrom($config['SMTP_EMAIL'], 'Test Mailer');
    $mail->addAddress($config['SMTP_EMAIL'], 'Test User');     // Add a recipient (sending to self)

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Test Email from NebulaDET';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
