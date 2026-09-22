<?php
/**
 * SMTP MAILER FACTURA
 * Archivo: /includes/goes_signer/mail_sender.php
 * Fecha: 2026-01-08
 */

/* use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

function enviar_factura_email(string $email, string $pdfPath): bool
{
    $config = require __DIR__ . '/../utils/config.php';

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $config['SMTP_EMAIL'];
    $mail->Password = $config['SMTP_PASSWORD'];
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom($config['SMTP_EMAIL'], 'Factura Electrónica');
    $mail->addAddress($email);

    $mail->Subject = 'Factura Electrónica';
    $mail->Body = 'Adjuntamos su factura electrónica.';
    $mail->addAttachment($pdfPath);

    return $mail->send();
}
 */

require_once __DIR__ . '/../../../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

function enviar_factura_email(string $to, string $pdfPath): bool
{
    $config = require __DIR__ . '/config.php';

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; 
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['SMTP_EMAIL'];
    $mail->Password   = $config['SMTP_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    
    // Debug settings
    $mail->SMTPDebug   = 2; 
    $mail->Debugoutput = 'error_log';

    $mail->setFrom($config['SMTP_EMAIL'], 'Facturación Electrónica');
    $mail->addAddress($to);

    $mail->Subject = 'Factura electrónica emitida';
    $mail->Body    = 'Adjuntamos su factura electrónica en formato PDF.';
    $mail->addAttachment($pdfPath);

    return $mail->send();
}
