<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* ================= LOAD PHPMailer ================= */

require __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
require __DIR__ . '/../lib/PHPMailer/src/Exception.php';

/* ================= LOAD CONFIG ================= */

$config = require __DIR__ . '/mail.php';

/* ================= SEND MAIL ================= */

function sendMail($to, $subject, $body, $attachments = null)
{
    global $config;

    $mail = new PHPMailer(true);

    try {
        /* ---------- SMTP ---------- */
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['port'];

        /* ---------- FROM / TO ---------- */
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to);

        /* ---------- ATTACHMENTS ---------- */
        if (!empty($attachments)) {

            // Multiple attachments
            if (is_array($attachments)) {
                foreach ($attachments as $file) {
                    if (is_string($file) && file_exists($file)) {
                        $mail->addAttachment($file);
                    }
                }
            }

            // Single attachment
            elseif (is_string($attachments) && file_exists($attachments)) {
                $mail->addAttachment($attachments);
            }
        }

        /* ---------- CONTENT ---------- */
        $mail->isHTML(false); // plain text (stable)
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;

    } catch (Exception $e) {
        // Uncomment ONLY if debugging:
        // error_log('Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
